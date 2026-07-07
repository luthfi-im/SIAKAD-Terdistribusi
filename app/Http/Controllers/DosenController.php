<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Krs;
use App\Models\Nilai;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class DosenController extends Controller
{
    public function kelasSaya(Request $request)
    {
        $nip = Auth::user()->ref_id;
        $kelas = Kelas::with('mataKuliah', 'ruangan')
            ->where('nip_dosen', $nip)
            ->get();

        return response()->json($kelas);
    }

    // Daftar peserta kelas
    public function pesertaKelas(int $idKelas)
    {
        $nip = Auth::user()->ref_id;

        Kelas::where('id_kelas', $idKelas)
            ->where('nip_dosen', $nip)
            ->firstOrFail();

        $peserta = Krs::with([
            'mahasiswa',
            'mahasiswa.nilai' => function ($q) use ($idKelas) {
                $q->where('id_kelas', $idKelas);
            }
        ])
            ->where('id_kelas', $idKelas)
            ->where('status', 'Sukses')
            ->get()
            ->map(function ($krs) use ($idKelas) {
                $nilai = $krs->mahasiswa->nilai->first(); // sudah difilter id_kelas di eager load
                return [
                    'nim' => $krs->mahasiswa->nim,
                    'mahasiswa' => $krs->mahasiswa,
                    'nilai_akhir' => $nilai->nilai_akhir ?? null,
                    'is_finalisasi' => $nilai->is_finalisasi ?? false,
                ];
            });

        return response()->json($peserta);
    }

    // Input/edit presensi
    public function inputPresensi(Request $request)
    {
        $conn = match (session('current_regional', 1)) {
            2 => 'pgsql_r2',
            3 => 'pgsql_r3',
            default => 'pgsql',
        };

        $validated = $request->validate([
            'id_kelas' => "required|integer|exists:{$conn}.kelas,id_kelas",
            'tanggal_pertemuan' => 'required|date',
            'pertemuan_ke' => 'required|integer|min:1|max:16',
            'kehadiran' => 'required|array',
            'kehadiran.*.nim' => "required|string|exists:{$conn}.mahasiswa,nim",
            'kehadiran.*.status' => ['required', Rule::in(['Hadir', 'Izin', 'Sakit', 'Alpa'])],
        ]);

        $nip = Auth::user()->ref_id;

        $kelas = Kelas::where('id_kelas', $validated['id_kelas'])
            ->where('nip_dosen', $nip)
            ->firstOrFail();

        foreach ($validated['kehadiran'] as $item) {
            Presensi::updateOrCreate(
                [
                    'id_kelas' => $validated['id_kelas'],
                    'nim' => $item['nim'],
                    'pertemuan_ke' => $validated['pertemuan_ke'],
                ],
                [
                    'id_regional' => $kelas->id_regional,
                    'tanggal_pertemuan' => $validated['tanggal_pertemuan'],
                    'status' => $item['status'],
                ]
            );
        }

        return response()->json(['message' => 'Presensi berhasil disimpan.']);
    }

    // Rekap presensi
    public function rekapPresensi(int $idKelas)
    {
        $nip = Auth::user()->ref_id;

        // ← BARIS BARU
        Kelas::where('id_kelas', $idKelas)
            ->where('nip_dosen', $nip)
            ->firstOrFail();

        $rekap = Presensi::where('id_kelas', $idKelas)
            ->selectRaw('nim, status, count(*) as jumlah')
            ->groupBy('nim', 'status')
            ->get()
            ->groupBy('nim');

        return response()->json($rekap);
    }

    // Input nilai
    public function inputNilai(Request $request)
    {
        $conn = match (session('current_regional', 1)) {
            2 => 'pgsql_r2',
            3 => 'pgsql_r3',
            default => 'pgsql',
        };

        $validated = $request->validate([
            'id_kelas' => "required|integer|exists:{$conn}.kelas,id_kelas",
            'nim' => "required|string|exists:{$conn}.mahasiswa,nim",
            'nilai_akhir' => 'required|numeric|min:0|max:100',
        ]);

        $nip = Auth::user()->ref_id;

        // ← BARIS BARU: ganti Kelas::find() jadi query dengan filter nip_dosen
        $kelas = Kelas::where('id_kelas', $validated['id_kelas'])
            ->where('nip_dosen', $nip)
            ->firstOrFail();

        $nilai = Nilai::where('id_kelas', $validated['id_kelas'])
            ->where('nim', $validated['nim'])
            ->first();

        if ($nilai && $nilai->is_finalisasi) {
            return response()->json([
                'message' => 'Nilai sudah difinalisasi dan tidak dapat diubah dosen. Hubungi BAAK untuk revisi.',
            ], 403);
        }

        Nilai::updateOrCreate(
            ['id_kelas' => $validated['id_kelas'], 'nim' => $validated['nim']],
            [
                'id_regional' => $kelas->id_regional,
                'nilai_akhir' => $validated['nilai_akhir'],
                'is_finalisasi' => false,
            ]
        );

        return response()->json(['message' => 'Nilai berhasil disimpan.']);
    }

    // Finalisasi nilai
    public function finalisasiNilai(int $idKelas)
    {
        $nip = Auth::user()->ref_id;

        // ← BARIS BARU
        Kelas::where('id_kelas', $idKelas)
            ->where('nip_dosen', $nip)
            ->firstOrFail();

        $belumDinilai = Krs::where('id_kelas', $idKelas)
            ->where('status', 'Sukses')
            ->whereDoesntHave('mahasiswa.nilai', function ($q) use ($idKelas) {
                $q->where('id_kelas', $idKelas);
            })
            ->count();

        if ($belumDinilai > 0) {
            return response()->json([
                'message' => "Masih ada {$belumDinilai} mahasiswa yang belum diberi nilai.",
            ], 422);
        }

        Nilai::where('id_kelas', $idKelas)->update([
            'is_finalisasi' => true,
            'finalisasi_at' => now(),
        ]);

        return response()->json(['message' => 'Nilai kelas berhasil difinalisasi dan bersifat permanen.']);
    }
}
