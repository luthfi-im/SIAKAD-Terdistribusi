<?php

namespace App\Http\Controllers;
use App\Jobs\ReplikasiMasterDataJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PusatController extends Controller
{
    private array $regionalConnections = [
        1 => 'pgsql',
        2 => 'pgsql_r2',
        3 => 'pgsql_r3',
    ];

    private array $namaRegional = [
        1 => 'Regional 1 — Teknik & Ilkom',
        2 => 'Regional 2 — Ekonomi & Bisnis',
        3 => 'Regional 3 — Kedokteran & Kes.',
    ];

    // Monitoring KRS lintas regional (REQ-4.2.2, REQ-6.1.3)
    public function monitoringKrs()
    {
        $hasil = [];

        foreach ($this->regionalConnections as $id => $conn) {
            try {
                $data = DB::connection($conn)->table('krs')
                    ->join('mahasiswa', 'krs.nim', '=', 'mahasiswa.nim')
                    ->join('kelas', 'krs.id_kelas', '=', 'kelas.id_kelas')
                    ->join('mata_kuliah', 'kelas.kode_mk', '=', 'mata_kuliah.kode_mk')
                    ->select(
                        'krs.id_krs',
                        'krs.nim',
                        'mahasiswa.nama_mahasiswa',
                        'mata_kuliah.kode_mk',
                        'mata_kuliah.nama_mk',
                        'krs.status',
                        'krs.created_at'
                    )
                    ->orderByDesc('krs.created_at')
                    ->limit(20)
                    ->get()
                    ->map(fn($row) => (array) $row + ['regional' => $this->namaRegional[$id]]);

                $hasil = array_merge($hasil, $data->toArray());
            } catch (\Exception $e) {
                // Regional gak bisa diakses (network partition / node down) — tetap tampilkan yang lain
                // Sesuai REQ-5.4.1: kegagalan satu regional tidak boleh menghentikan yang lain
                continue;
            }
        }

        usort($hasil, fn($a, $b) => strtotime($b['created_at']) <=> strtotime($a['created_at']));

        return response()->json(array_slice($hasil, 0, 30));
    }

    // Monitoring Nilai lintas regional (REQ-4.2.2)
    public function monitoringNilai()
    {
        $hasil = [];

        foreach ($this->regionalConnections as $id => $conn) {
            try {
                $data = DB::connection($conn)->table('nilai')
                    ->join('mahasiswa', 'nilai.nim', '=', 'mahasiswa.nim')
                    ->join('kelas', 'nilai.id_kelas', '=', 'kelas.id_kelas')
                    ->join('mata_kuliah', 'kelas.kode_mk', '=', 'mata_kuliah.kode_mk')
                    ->select(
                        'nilai.id_nilai',
                        'nilai.nim',
                        'mahasiswa.nama_mahasiswa',
                        'mata_kuliah.kode_mk',
                        'mata_kuliah.nama_mk',
                        'nilai.nilai_akhir',
                        'nilai.is_finalisasi'
                    )
                    ->orderByDesc('nilai.id_nilai')
                    ->limit(20)
                    ->get()
                    ->map(fn($row) => (array) $row + ['regional' => $this->namaRegional[$id]]);

                $hasil = array_merge($hasil, $data->toArray());
            } catch (\Exception $e) {
                continue;
            }
        }

        return response()->json(array_slice($hasil, 0, 30));
    }

    // Ringkasan statistik per regional
    public function ringkasan()
    {
        $stats = [];

        foreach ($this->regionalConnections as $id => $conn) {
            try {
                $stats[] = [
                    'regional' => $this->namaRegional[$id],
                    'total_mahasiswa' => DB::connection($conn)->table('mahasiswa')->count(),
                    'total_kelas' => DB::connection($conn)->table('kelas')->count(),
                    'total_krs_sukses' => DB::connection($conn)->table('krs')->where('status', 'Sukses')->count(),
                    'total_nilai_final' => DB::connection($conn)->table('nilai')->where('is_finalisasi', true)->count(),
                    'status' => 'online',
                ];
            } catch (\Exception $e) {
                $stats[] = [
                    'regional' => $this->namaRegional[$id],
                    'status' => 'offline',
                ];
            }
        }

        return response()->json($stats);
    }

    // ===== MATA KULIAH =====

    public function daftarMataKuliahPerRegional()
    {
        $hasil = [];
        foreach ($this->regionalConnections as $id => $conn) {
            try {
                $hasil[$id] = [
                    'nama' => $this->namaRegional[$id],
                    'data' => DB::connection($conn)->table('mata_kuliah')->get(),
                    'status' => 'online',
                ];
            } catch (\Exception $e) {
                $hasil[$id] = ['nama' => $this->namaRegional[$id], 'data' => [], 'status' => 'offline'];
            }
        }
        return response()->json($hasil);
    }
    public function storeMataKuliah(Request $request)
    {
        $validated = $request->validate([
            'kode_mk' => 'required|string|max:20',
            'nama_mk' => 'required|string|max:200',
            'id_prodi' => 'required|string|max:20',
            'sks' => 'required|integer|min:1|max:6',
            'kode_mk_prasyarat' => 'nullable|string|max:20',
            'target_regional' => 'required|array|min:1',
            'target_regional.*' => 'integer|between:1,3',
        ]);

        $targetRegional = $validated['target_regional'];
        unset($validated['target_regional']);

        $data = array_merge($validated, ['created_at' => now(), 'updated_at' => now()]);

        // Arsip di Pusat (rujukan, bukan sumber query mahasiswa)
        DB::connection('pgsql_pusat')->table('mata_kuliah')->updateOrInsert(
            ['kode_mk' => $validated['kode_mk']],
            $data
        );

        ReplikasiMasterDataJob::dispatch('mata_kuliah', 'kode_mk', $data, $targetRegional);

        $namaTarget = collect($targetRegional)->map(fn($id) => $this->namaRegional[$id])->implode(', ');

        return response()->json(['message' => "Mata kuliah disimpan dan direplikasi ke: {$namaTarget}."]);
    }

    // ===== KELOLA AKUN (mahasiswa/dosen/baak per regional) =====

    public function daftarUser(Request $request)
    {
        $idRegional = (int) $request->query('regional', 1);
        $role = $request->query('role'); // opsional
        $conn = $this->regionalConnections[$idRegional] ?? 'pgsql';

        $query = DB::connection($conn)->table('users')
            ->select('id', 'name', 'email', 'role', 'ref_id');

        if ($role) {
            $query->where('role', $role);
        }

        return response()->json($query->orderBy('role')->get());
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'id_regional' => 'required|integer|between:1,3',
            'name' => 'required|string|max:200',
            'email' => 'required|email|max:200',
            'password' => 'required|string|min:6',
            'role' => 'required|in:mahasiswa,dosen,baak',
            'ref_id' => 'nullable|string|max:20',
        ]);

        $conn = $this->regionalConnections[$validated['id_regional']];

        $exists = DB::connection($conn)->table('users')->where('email', $validated['email'])->exists();
        if ($exists) {
            return response()->json(['message' => 'Email sudah terdaftar di regional ini.'], 422);
        }

        DB::connection($conn)->table('users')->insert([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => $validated['role'],
            'ref_id' => $validated['ref_id'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => "Akun berhasil dibuat di {$this->namaRegional[$validated['id_regional']]}."]);
    }

    public function daftarRuanganPerRegional()
    {
        $hasil = [];
        foreach ($this->regionalConnections as $id => $conn) {
            try {
                $hasil[$id] = [
                    'nama' => $this->namaRegional[$id],
                    'data' => DB::connection($conn)->table('ruangan')->get(),
                    'status' => 'online',
                ];
            } catch (\Exception $e) {
                $hasil[$id] = ['nama' => $this->namaRegional[$id], 'data' => [], 'status' => 'offline'];
            }
        }
        return response()->json($hasil);
    }

    public function storeRuangan(Request $request)
    {
        $validated = $request->validate([
            'id_ruangan' => 'required|string|max:20',
            'nama_ruangan' => 'required|string|max:100',
            'kapasitas' => 'required|integer|min:1',
            'target_regional' => 'required|array|min:1',
            'target_regional.*' => 'integer|between:1,3',
        ]);

        $targetRegional = $validated['target_regional'];
        unset($validated['target_regional']);

        foreach ($targetRegional as $idRegional) {
            $data = array_merge($validated, [
                'id_regional' => $idRegional,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::connection('pgsql_pusat')->table('ruangan')->updateOrInsert(
                ['id_ruangan' => $validated['id_ruangan']],
                $data
            );

            ReplikasiMasterDataJob::dispatch('ruangan', 'id_ruangan', $data, [$idRegional]);
        }

        $namaTarget = collect($targetRegional)->map(fn($id) => $this->namaRegional[$id])->implode(', ');
        return response()->json(['message' => "Ruangan disimpan dan direplikasi ke: {$namaTarget}."]);
    }

    public function daftarDosenPerRegional()
    {
        $hasil = [];
        foreach ($this->regionalConnections as $id => $conn) {
            try {
                $hasil[$id] = [
                    'nama' => $this->namaRegional[$id],
                    'data' => DB::connection($conn)->table('dosen')->get(),
                    'status' => 'online',
                ];
            } catch (\Exception $e) {
                $hasil[$id] = ['nama' => $this->namaRegional[$id], 'data' => [], 'status' => 'offline'];
            }
        }
        return response()->json($hasil);
    }

    public function storeDosen(Request $request)
    {
        $validated = $request->validate([
            'nip' => 'required|string|max:20',
            'nama_dosen' => 'required|string|max:200',
            'id_prodi' => 'required|string|max:20',
            'target_regional' => 'required|array|min:1',
            'target_regional.*' => 'integer|between:1,3',
        ]);

        $targetRegional = $validated['target_regional'];
        unset($validated['target_regional']);

        foreach ($targetRegional as $idRegional) {
            $data = array_merge($validated, [
                'id_regional' => $idRegional,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::connection('pgsql_pusat')->table('dosen')->updateOrInsert(
                ['nip' => $validated['nip']],
                $data
            );

            ReplikasiMasterDataJob::dispatch('dosen', 'nip', $data, [$idRegional]);
        }

        $namaTarget = collect($targetRegional)->map(fn($id) => $this->namaRegional[$id])->implode(', ');
        return response()->json(['message' => "Dosen disimpan dan direplikasi ke: {$namaTarget}."]);
    }

    public function sinkronSekarang()
    {
        \Illuminate\Support\Facades\Artisan::call('sinkron:pusat', ['--wait' => 3]); // wait dipersingkat khusus utk demo

        $ringkasan = [
            'krs_konsolidasi' => DB::connection('pgsql_pusat')->table('krs_konsolidasi')->count(),
            'nilai_konsolidasi' => DB::connection('pgsql_pusat')->table('nilai_konsolidasi')->count(),
        ];

        return response()->json(['message' => 'Sinkronisasi selesai dijalankan.', 'ringkasan' => $ringkasan]);
    }
}
