<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Dosen;
use App\Models\KalenderAkademik;
use App\Models\MataKuliah;
use App\Models\Nilai;
use App\Models\Ruangan;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class BaakController extends Controller
{
    /**
     * Menentukan koneksi database sesuai regional yang sedang aktif di session
     * (di-set oleh middleware DetectRegional berdasarkan subdomain).
     */
    private function conn(): string
    {
        return match (session('current_regional', 1)) {
            2 => 'pgsql_r2',
            3 => 'pgsql_r3',
            default => 'pgsql',
        };
    }

    // ===== MASTER DATA: DOSEN =====

    public function daftarDosen()
    {
        return response()->json(
            Dosen::on($this->conn())->orderBy('nama_dosen')->get()
        );
    }

    public function storeDosen(Request $request)
    {
        $conn = $this->conn();
        $idRegional = session('current_regional', 1);

        $validated = $request->validate([
            'nip' => "required|string|max:20|unique:{$conn}.dosen,nip",
            'id_prodi' => 'required|string|max:20',
            'nama_dosen' => 'required|string|max:200',
        ]);

        $dosen = Dosen::on($conn)->create([
            ...$validated,
            'id_regional' => $idRegional,
        ]);

        return response()->json(['message' => 'Dosen berhasil ditambahkan.', 'data' => $dosen], 201);
    }

    public function toggleDosenAktif(Request $request, string $nip)
    {
        $dosen = Dosen::on($this->conn())->findOrFail($nip);
        $dosen->update(['is_deleted' => $request->boolean('is_deleted')]);

        return response()->json([
            'message' => $dosen->is_deleted ? 'Dosen dinonaktifkan.' : 'Dosen diaktifkan kembali.',
            'data' => $dosen,
        ]);
    }

    // ===== MASTER DATA: MATA KULIAH =====

    public function storeMataKuliah(Request $request)
    {
        $validated = $request->validate([
            'kode_mk' => 'required|string|max:20|unique:mata_kuliah,kode_mk',
            'kode_mk_prasyarat' => 'nullable|string|exists:mata_kuliah,kode_mk',
            'id_prodi' => 'required|string|max:20',
            'nama_mk' => 'required|string|max:200',
            'sks' => 'required|integer|min:1|max:6',
            'semester' => 'required|integer|min:1|max:8',
        ]);

        $mk = MataKuliah::create($validated);

        return response()->json(['message' => 'Mata kuliah berhasil ditambahkan.', 'data' => $mk], 201);
    }

    public function updateMataKuliah(Request $request, string $kodeMk)
    {
        $conn = $this->conn();

        $mk = MataKuliah::on($conn)->findOrFail($kodeMk);
        $validated = $request->validate([
            'nama_mk' => 'sometimes|string|max:200',
            'sks' => 'sometimes|integer|min:1|max:6',
            'kode_mk_prasyarat' => "nullable|string|exists:{$conn}.mata_kuliah,kode_mk",
        ]);
        $mk->update($validated);

        // REQ-4.2.1: perubahan master data wajib direplikasi <5 detik ke node Regional
        // (TODO: dispatch job replikasi ke Regional 2 & 3 saat arsitektur multi-node aktif)

        return response()->json(['message' => 'Mata kuliah berhasil diperbarui.', 'data' => $mk]);
    }

    public function daftarMataKuliahRegional()
    {
        return response()->json(
            DB::connection($this->currentConnection())->table('mata_kuliah')
                ->orderBy('semester')
                ->orderBy('kode_mk')
                ->get()
        );
    }

    public function storeKelas(Request $request)
    {
        $validated = $request->validate([
            'kode_mk' => 'required|string|exists:mata_kuliah,kode_mk',
            'nip_dosen' => 'required|string|exists:dosen,nip',
            'id_ruangan' => 'required|string|exists:ruangan,id_ruangan',
            'semester' => 'required|integer|in:1,2', // 1=ganjil, 2=genap
            'tahun_akademik' => 'required|integer|min:2020|max:2100',
            'kuota' => 'required|integer|min:1',
        ]);

        $idRegional = session('current_regional', 1);

        $kelas = Kelas::create([
            'id_regional' => $idRegional,
            'kode_mk' => $validated['kode_mk'],
            'nip_dosen' => $validated['nip_dosen'],
            'id_ruangan' => $validated['id_ruangan'],
            'semester' => $validated['semester'],
            'tahun_akademik' => $validated['tahun_akademik'],
            'kuota' => $validated['kuota'],
            'sisa_kuota' => $validated['kuota'],
        ]);

        return response()->json(['message' => 'Kelas berhasil dibuka.', 'data' => $kelas], 201);
    }

    public function daftarKelasLengkap()
    {
        return response()->json(
            Kelas::with('mataKuliah', 'dosen', 'ruangan')
                ->orderByDesc('id_kelas')
                ->get()
        );
    }

    // ===== MASTER DATA: KALENDER AKADEMIK =====

    public function toggleKalender(Request $request, int $id)
    {
        $kalender = KalenderAkademik::on($this->conn())->findOrFail($id);
        $kalender->update(['status_aktif' => $request->boolean('status_aktif')]);

        return response()->json(['message' => 'Status kalender akademik diperbarui.', 'data' => $kalender]);
    }

    public function daftarKalender()
    {
        return response()->json(
            KalenderAkademik::on($this->conn())->orderByDesc('id')->get()
        );
    }

    public function storeKalender(Request $request)
    {
        $conn = $this->conn();

        $validated = $request->validate([
            'semester' => 'required|string|max:30',
            'tahun_ajaran' => 'required|string|max:20',
            'status_aktif' => 'boolean',
        ]);

        $kalender = KalenderAkademik::on($conn)->create([
            'semester' => $validated['semester'],
            'tahun_ajaran' => $validated['tahun_ajaran'],
            'status_aktif' => $validated['status_aktif'] ?? false,
        ]);

        return response()->json([
            'message' => 'Periode akademik berhasil ditambahkan.',
            'data' => $kalender,
        ], 201);
    }

    // ===== MASTER DATA: RUANGAN =====

    public function daftarRuangan()
    {
        return response()->json(
            Ruangan::on($this->conn())->orderBy('id_ruangan')->get()
        );
    }

    public function storeRuangan(Request $request)
    {
        $conn = $this->conn();
        $idRegional = session('current_regional', 1);

        $validated = $request->validate([
            'id_ruangan' => "required|string|max:20|unique:{$conn}.ruangan,id_ruangan",
            'nama_ruangan' => 'required|string|max:100',
            'kapasitas' => 'required|integer|min:1',
        ]);

        $ruangan = Ruangan::on($conn)->create([
            ...$validated,
            'id_regional' => $idRegional,
        ]);

        return response()->json(['message' => 'Ruangan berhasil ditambahkan.', 'data' => $ruangan], 201);
    }

    public function daftarKelas()
    {
        $conn = $this->conn();

        $kelas = \App\Models\Kelas::on($conn)->get()
            ->map(function ($k) use ($conn) {
                $mk = \App\Models\MataKuliah::on($conn)->find($k->kode_mk);
                $dosen = Dosen::on($conn)->find($k->nip_dosen);

                return [
                    'id_kelas' => $k->id_kelas,
                    'kode_mk' => $mk->kode_mk ?? '(tidak ditemukan)',
                    'nama_mk' => $mk->nama_mk ?? '(tidak ditemukan)',
                    'dosen' => $dosen->nama_dosen ?? '(tidak ditemukan)',
                ];
            });

        return response()->json($kelas);
    }

    // ===== REVISI NILAI PASCA-FINALISASI (REQ-5.5.4) =====

    public function revisiNilai(Request $request)
    {
        $conn = $this->conn();

        $validated = $request->validate([
            'id_kelas' => "required|integer|exists:{$conn}.kelas,id_kelas",
            'nim' => "required|string|exists:{$conn}.mahasiswa,nim",
            'nilai_akhir' => 'required|numeric|min:0|max:100',
            // admin_id DIHAPUS dari validasi — nggak lagi dipercaya dari client
        ]);

        $adminId = Auth::user()->ref_id
            ?? ('BAAK-' . Auth::user()->id);

        $nilai = Nilai::on($conn)
            ->where('id_kelas', $validated['id_kelas'])
            ->where('nim', $validated['nim'])
            ->firstOrFail();

        $oldValue = $nilai->toArray();

        $nilai->update(['nilai_akhir' => $validated['nilai_akhir']]);

        // AUDIT_LOG adalah tabel sistem yang hanya ada di node Pusat (pgsql) — TIDAK pakai $conn
        AuditLog::create([
            'user_id' => $adminId,
            'role_user' => 'BAAK',
            'aktivitas' => "Revisi nilai akhir kelas {$validated['id_kelas']} untuk NIM {$validated['nim']}",
            'ip_address' => $request->ip(),
            'old_value' => $oldValue,
        ]);

        return response()->json(['message' => 'Nilai berhasil direvisi oleh BAAK.', 'data' => $nilai]);
    }

    // ===== AUDIT LOG (read-only) =====

    public function auditLog()
    {
        // AUDIT_LOG hanya ada di node Pusat (pgsql) — tidak scoped ke regional
        return response()->json(
            AuditLog::orderByDesc('created_at')->limit(50)->get()
        );
    }

    // ===== EKSPOR PDDIKTI (REQ-6.3.1) =====

    public function eksporPddikti(Request $request)
    {
        $conn = $this->conn();
        $idRegional = session('current_regional', 1);
        $tahunAkademik = $request->query('tahun_akademik', 2025);

        $data = Nilai::on($conn)->with('mahasiswa', 'kelas.mataKuliah')
            ->whereHas('kelas', fn($q) => $q->where('tahun_akademik', $tahunAkademik))
            ->where('is_finalisasi', true)
            ->get()
            ->map(fn($n) => [
                'nim' => $n->nim,
                'nama_mahasiswa' => $n->mahasiswa->nama_mahasiswa,
                'kode_mk' => $n->kelas->mataKuliah->kode_mk,
                'nama_mk' => $n->kelas->mataKuliah->nama_mk,
                'sks' => $n->kelas->mataKuliah->sks,
                'nilai_akhir' => $n->nilai_akhir,
                'tahun_akademik' => $tahunAkademik,
            ]);

        // Sesuai REQ-6.3.1: format JSON terstruktur (representasi sebelum dienkripsi/dikirim ke Neo Feeder)
        return response()->json([
            'sumber' => 'SIAKAD Regional ' . $idRegional,
            'tahun_akademik' => $tahunAkademik,
            'jumlah_record' => $data->count(),
            'data' => $data,
        ]);
    }


    private function currentConnection(): string
    {
        return match (session('current_regional', 1)) {
            2 => 'pgsql_r2',
            3 => 'pgsql_r3',
            default => 'pgsql',
        };
    }

    public function daftarStatusKeuangan()
    {
        $conn = $this->currentConnection();

        $data = DB::connection($conn)->table('mahasiswa')
            ->leftJoin('status_keuangan', 'mahasiswa.nim', '=', 'status_keuangan.nim')
            ->select('mahasiswa.nim', 'mahasiswa.nama_mahasiswa', DB::raw("COALESCE(status_keuangan.status, 'LUNAS') as status"))
            ->get();

        return response()->json($data);
    }

    public function toggleStatusKeuangan(Request $request)
    {
        $conn = $this->currentConnection();

        $validated = $request->validate([
            'nim' => 'required|string',
            'status' => 'required|in:LUNAS,BELUM LUNAS',
        ]);

        // Cek mahasiswa exists di regional yang benar (bukan pakai exists:mahasiswa,nim generic)
        $ada = DB::connection($conn)->table('mahasiswa')->where('nim', $validated['nim'])->exists();
        if (!$ada) {
            return response()->json(['message' => 'Mahasiswa tidak ditemukan di regional ini.'], 404);
        }

        DB::connection($conn)->table('status_keuangan')->updateOrInsert(
            ['nim' => $validated['nim']],
            ['status' => $validated['status'], 'updated_at' => now()]
        );

        Cache::forget("status_keuangan:{$validated['nim']}");

        return response()->json(['message' => "Status keuangan {$validated['nim']} diubah jadi {$validated['status']}."]);
    }
}
