<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Regional3Seeder extends Seeder
{
    public function run(): void
    {
        $conn = 'pgsql_r3';
        $now = Carbon::now();

        // Regional (tidak ada timestamps di tabel ini)
        DB::connection($conn)->table('regional')->insert([
            'id_regional' => 3,
            'nama_regional' => 'Regional 3 - Kampus Cabang Timur',
            'lokasi' => 'Jl. Kesehatan No. 3',
            'fakultas' => 'Fakultas Kedokteran dan Ilmu Kesehatan',
        ]);

        // Dosen
        DB::connection($conn)->table('dosen')->insert([
            [
                'nip' => 'D201',
                'id_regional' => 3,
                'id_prodi' => 'KD',
                'nama_dosen' => 'dr. Anisa Putri, Sp.PD',
                'is_deleted' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nip' => 'D202',
                'id_regional' => 3,
                'id_prodi' => 'KP',
                'nama_dosen' => 'dr. Hendra Wijaya, M.Kes.',
                'is_deleted' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // Ruangan
        DB::connection($conn)->table('ruangan')->insert([
            [
                'id_ruangan' => 'GD-C-301',
                'id_regional' => 3,
                'nama_ruangan' => 'Gedung C - Ruang 301',
                'kapasitas' => 30,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_ruangan' => 'GD-C-302',
                'id_regional' => 3,
                'nama_ruangan' => 'Gedung C - Lab Anatomi',
                'kapasitas' => 20,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // Mata Kuliah
        DB::connection($conn)->table('mata_kuliah')->insert([
            [
                'kode_mk' => 'KD101',
                'kode_mk_prasyarat' => null,
                'id_prodi' => 'KD',
                'nama_mk' => 'Anatomi Dasar',
                'sks' => 4,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode_mk' => 'KP101',
                'kode_mk_prasyarat' => null,
                'id_prodi' => 'KP',
                'nama_mk' => 'Dasar Keperawatan',
                'sks' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode_mk' => 'KD201',
                'kode_mk_prasyarat' => 'KD101',
                'id_prodi' => 'KD',
                'nama_mk' => 'Fisiologi Manusia',
                'sks' => 4,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // Kelas
        DB::connection($conn)->table('kelas')->insert([
            [
                'id_regional' => 3,
                'kode_mk' => 'KD101',
                'nip_dosen' => 'D201',
                'id_ruangan' => 'GD-C-301',
                'semester' => 1,
                'tahun_akademik' => 2025,
                'kuota' => 30,
                'sisa_kuota' => 28,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_regional' => 3,
                'kode_mk' => 'KP101',
                'nip_dosen' => 'D202',
                'id_ruangan' => 'GD-C-302',
                'semester' => 1,
                'tahun_akademik' => 2025,
                'kuota' => 20,
                'sisa_kuota' => 20,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // Mahasiswa
        DB::connection($conn)->table('mahasiswa')->insert([
            [
                'nim' => '2401001',
                'id_regional' => 3,
                'id_prodi' => 'KD',
                'nama_mahasiswa' => 'Amelia Putri Lestari',
                'angkatan' => 2024,
                'ips_terakhir' => 3.75,
                'is_deleted' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nim' => '2401002',
                'id_regional' => 3,
                'id_prodi' => 'KP',
                'nama_mahasiswa' => 'Rizky Aditya Nugraha',
                'angkatan' => 2024,
                'ips_terakhir' => 3.10,
                'is_deleted' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // Kalender Akademik
        DB::connection($conn)->table('kalender_akademik')->insert([
            'semester' => 'Ganjil',
            'tahun_ajaran' => '2025/2026',
            'status_aktif' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
