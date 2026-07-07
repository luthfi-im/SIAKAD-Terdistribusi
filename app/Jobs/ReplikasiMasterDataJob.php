<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReplikasiMasterDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private array $connectionMap = [1 => 'pgsql', 2 => 'pgsql_r2', 3 => 'pgsql_r3'];

    public function __construct(
        public string $tabel,
        public string $primaryKey,
        public array $data,
        public array $targetRegional // contoh: [1, 2] — cuma replikasi ke Regional 1 & 2
    ) {}

    public function handle(): void
    {
        foreach ($this->targetRegional as $idRegional) {
            $conn = $this->connectionMap[$idRegional] ?? null;
            if (!$conn) continue;

            try {
                DB::connection($conn)->table($this->tabel)->updateOrInsert(
                    [$this->primaryKey => $this->data[$this->primaryKey]],
                    $this->data
                );

                Log::info("Replikasi {$this->tabel} berhasil ke Regional {$idRegional}: {$this->data[$this->primaryKey]}");
            } catch (\Exception $e) {
                Log::error("Replikasi {$this->tabel} GAGAL ke Regional {$idRegional}: {$e->getMessage()}");
            }
        }
    }
}
