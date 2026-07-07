<?php

namespace App\Jobs;

use App\Models\Kelas;
use App\Models\Krs;
use App\Models\MataKuliah;
use App\Models\Nilai;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProsesKrsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $nim;
    public int $idKelas;
    public int $idRegional;

    public function __construct(string $nim, int $idKelas, int $idRegional)
    {
        $this->nim = $nim;
        $this->idKelas = $idKelas;
        $this->idRegional = $idRegional;
    }

    private function conn(): string
    {
        return match ($this->idRegional) {
            2 => 'pgsql_r2',
            3 => 'pgsql_r3',
            default => 'pgsql',
        };
    }

    public function handle(): void
    {
        DB::connection($this->conn())->transaction(function () {
            // Cegah duplikasi: kalau NIM ini sudah pernah ambil kelas ini dengan status Sukses, tolak.
            $sudahAmbil = Krs::on($this->conn())
                ->where('nim', $this->nim)
                ->where('id_kelas', $this->idKelas)
                ->where('status', 'Sukses')
                ->lockForUpdate()
                ->exists();

            if ($sudahAmbil) {
                $this->simpanKrs('Gagal', 'Mahasiswa sudah terdaftar di kelas ini');
                Log::warning("KRS DITOLAK: NIM {$this->nim} sudah terdaftar di kelas {$this->idKelas} (duplicate request).");
                return;
            }

            $kelas = Kelas::on($this->conn())->where('id_kelas', $this->idKelas)->lockForUpdate()->first();

            if (!$kelas) {
                Log::warning("KRS GAGAL: Kelas {$this->idKelas} tidak ditemukan di regional {$this->idRegional}.");
                return;
            }

            if (!$this->cekSisaKuota($kelas)) {
                $this->simpanKrs('Gagal', 'Kuota kelas penuh');
                return;
            }

            if (!$this->cekPrasyaratLulus()) {
                $this->simpanKrs('Gagal', 'Prasyarat mata kuliah belum terpenuhi');
                return;
            }

            $this->simpanKrs('Sukses');
            $kelas->decrement('sisa_kuota');
        });
    }

    private function cekSisaKuota(Kelas $kelas): bool
    {
        return $kelas->sisa_kuota > 0;
    }

    private function cekPrasyaratLulus(): bool
    {
        $kelas = Kelas::on($this->conn())->find($this->idKelas);
        $mk = MataKuliah::on($this->conn())->find($kelas->kode_mk);

        if (!$mk->kode_mk_prasyarat) {
            return true;
        }

        $nilaiPrasyarat = Nilai::on($this->conn())
            ->where('nim', $this->nim)
            ->whereHas('kelas', fn($q) => $q->where('kode_mk', $mk->kode_mk_prasyarat))
            ->where('is_finalisasi', true)
            ->first();

        if (!$nilaiPrasyarat) {
            return false;
        }

        return $nilaiPrasyarat->nilai_akhir >= 60;
    }

    private function simpanKrs(string $status, ?string $catatan = null): void
    {
        Krs::on($this->conn())->create([
            'id_regional' => $this->idRegional,
            'nim' => $this->nim,
            'id_kelas' => $this->idKelas,
            'status' => $status,
        ]);

        Log::info("KRS {$status} - Regional: {$this->idRegional}, NIM: {$this->nim}, Kelas: {$this->idKelas}" . ($catatan ? " ({$catatan})" : ""));
    }
}
