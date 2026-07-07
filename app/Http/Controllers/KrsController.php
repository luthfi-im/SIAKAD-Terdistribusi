<?php

namespace App\Http\Controllers;

use App\Jobs\ProsesKrsJob;
use App\Models\Kelas;
use App\Models\Krs;
use App\Models\Mahasiswa;
use App\Models\Nilai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KrsController extends Controller
{
    public function dashboard(Request $request)
    {
        $nim = Auth::user()->ref_id;
        $mahasiswa = Mahasiswa::findOrFail($nim);
        $kelasTersedia = Kelas::with(['mataKuliah', 'dosen', 'ruangan'])
            ->where('id_regional', $mahasiswa->id_regional)
            ->get();
        $krsSaya = Krs::with('kelas.mataKuliah')->where('nim', $nim)->get();

        return response()->json([
            'mahasiswa' => $mahasiswa,
            'kelas_tersedia' => $kelasTersedia,
            'krs_saya' => $krsSaya,
        ]);
    }

    public function ambilKelas(Request $request)
    {
        $validated = $request->validate([
            'id_kelas' => 'required|integer|exists:kelas,id_kelas',
        ]);
        $nim = auth()->user()->ref_id;
        $idRegional = session('current_regional', 1);

        // REQ-4.1.1: cek status keuangan (via cache, di-refresh cron 01:00 — REQ 3.4)
        if (!$this->cekStatusKeuanganCache($nim)) {
            return response()->json([
                'message' => 'Anda memiliki tunggakan UKT. Akses KRS ditutup.',
            ], 403);
        }

        // REQ-5.5.1: cek periode KRS aktif
        if (!$this->cekKalenderAktif()) {
            return response()->json([
                'message' => 'Periode pengisian KRS sedang tidak aktif.',
            ], 403);
        }

        // REQ-4.1.3: push ke antrean, bukan insert langsung
        ProsesKrsJob::dispatch($nim, $validated['id_kelas'], $idRegional);

        return response()->json([
            'message' => 'Permintaan sedang diproses. Cek status KRS Anda beberapa saat lagi.',
        ], 202);
    }

    private function cekStatusKeuanganCache(string $nim): bool
    {
        return Cache::remember("status_keuangan:{$nim}", 300, function () use ($nim) {
            $status = DB::table('status_keuangan')->where('nim', $nim)->value('status');
            // Default LUNAS kalau belum pernah di-set BAAK (REQ 3.3: kontrak API asli)
            return ($status ?? 'LUNAS') === 'LUNAS';
        });
    }

    private function cekKalenderAktif(): bool
    {
        return \App\Models\KalenderAkademik::where('status_aktif', true)->exists();
    }

    public function nilaiSaya()
    {
        $nim = Auth::user()->ref_id;

        $nilai = Nilai::with('kelas.mataKuliah')
            ->where('nim', $nim)
            ->get()
            ->map(fn($n) => [
                'kode_mk' => $n->kelas->mataKuliah->kode_mk,
                'nama_mk' => $n->kelas->mataKuliah->nama_mk,
                'sks' => $n->kelas->mataKuliah->sks,
                'nilai_akhir' => $n->nilai_akhir,
                'grade' => $this->hitungGrade($n->nilai_akhir),
                'is_finalisasi' => $n->is_finalisasi,
            ]);

        $sksLulus = $nilai->where('is_finalisasi', true)->sum('sks');
        $totalBobot = $nilai->where('is_finalisasi', true)
            ->sum(fn($n) => $n['sks'] * $this->bobotGrade($n['grade']));
        $ipk = $sksLulus > 0 ? round($totalBobot / $sksLulus, 2) : 0;

        return response()->json([
            'nilai' => $nilai,
            'ipk' => $ipk,
            'total_sks_lulus' => $sksLulus,
        ]);
    }

    private function hitungGrade(?float $nilaiAkhir): ?string
    {
        if ($nilaiAkhir === null)
            return null;
        return match (true) {
            $nilaiAkhir >= 85 => 'A',
            $nilaiAkhir >= 80 => 'AB',
            $nilaiAkhir >= 75 => 'B',
            $nilaiAkhir >= 70 => 'BC',
            $nilaiAkhir >= 60 => 'C',
            $nilaiAkhir >= 50 => 'D',
            default => 'E',
        };
    }

    private function bobotGrade(?string $grade): float
    {
        return match ($grade) {
            'A' => 4.0, 'AB' => 3.5, 'B' => 3.0, 'BC' => 2.5,
            'C' => 2.0, 'D' => 1.0, 'E' => 0.0,
            default => 0.0,
        };
    }
}
