<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SinkronisasiKePusat extends Command
{
    protected $signature = 'sinkron:pusat {--wait=300}';
    protected $description = 'Sinkronisasi KRS & Nilai dari semua Node Regional ke GCS Pusat (REQ-4.2.2)';

    private array $regions = [1 => 'pgsql', 2 => 'pgsql_r2', 3 => 'pgsql_r3'];

    public function handle(): int
    {
        $wait = (int) $this->option('wait');

        foreach ($this->regions as $idRegional => $conn) {
            $this->info("Sinkronisasi Regional {$idRegional}...");
            $this->syncWithRetry($idRegional, $conn, $wait);
        }

        $this->info('Sinkronisasi selesai.');
        return self::SUCCESS;
    }

    private function syncWithRetry(int $idRegional, string $conn, int $wait): void
    {
        $maxAttempts = 3;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                $this->syncKrs($idRegional, $conn);
                $this->syncNilai($idRegional, $conn);
                Log::info("Sinkronisasi Regional {$idRegional} berhasil (percobaan ke-{$attempt}).");
                return;
            } catch (\Exception $e) {
                Log::warning("Sinkronisasi Regional {$idRegional} gagal percobaan ke-{$attempt}: {$e->getMessage()}");
                if ($attempt < $maxAttempts) {
                    sleep($wait); // REQ-4.2.3: interval retry
                }
            }
        }

        // REQ-4.2.4: gagal 3x -> abort, tulis CRITICAL ke audit_log, simulasi alert DBA
        DB::connection('pgsql_pusat')->table('audit_log')->insert([
            'user_id' => 'SYSTEM',
            'role_user' => 'SYSTEM',
            'aktivitas' => "CRITICAL: Sinkronisasi Regional {$idRegional} gagal setelah 3 percobaan.",
            'ip_address' => '127.0.0.1',
            'old_value' => null,
            'created_at' => now(),
        ]);

        Log::critical("Sinkronisasi Regional {$idRegional} GAGAL TOTAL. [SIMULASI] Email alert dikirim ke DBA.");
    }

    private function syncKrs(int $idRegional, string $conn): void
    {
        DB::connection($conn)->table('krs')->orderBy('id_krs')->get()->each(function ($row) use ($idRegional) {
            DB::connection('pgsql_pusat')->table('krs_konsolidasi')->updateOrInsert(
                ['id_regional' => $idRegional, 'source_id_krs' => $row->id_krs],
                [
                    'nim' => $row->nim,
                    'id_kelas' => $row->id_kelas,
                    'status' => $row->status,
                    'source_created_at' => $row->created_at,
                    'synced_at' => now(),
                ]
            );
        });
    }

    private function syncNilai(int $idRegional, string $conn): void
    {
        DB::connection($conn)->table('nilai')->orderBy('id_nilai')->get()->each(function ($row) use ($idRegional) {
            DB::connection('pgsql_pusat')->table('nilai_konsolidasi')->updateOrInsert(
                ['id_regional' => $idRegional, 'source_id_nilai' => $row->id_nilai],
                [
                    'nim' => $row->nim,
                    'id_kelas' => $row->id_kelas,
                    'nilai_akhir' => $row->nilai_akhir,
                    'is_finalisasi' => $row->is_finalisasi,
                    'synced_at' => now(),
                ]
            );
        });
    }
}
