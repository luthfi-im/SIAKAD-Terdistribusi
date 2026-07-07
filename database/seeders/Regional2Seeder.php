<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Regional2Seeder extends Seeder
{
    public function run(): void
    {
        $conn = 'pgsql_r2';
        $now = Carbon::now();

        // Regional (tidak ada timestamps di tabel ini)
        DB::connection($conn)->table('regional')->insert([
            'id_regional' => 2,
            'nama_regional' => 'Regional 2 - Kampus Cabang Selatan',
            'lokasi' => 'Jl. Ekonomi Raya No. 2',
            'fakultas' => 'Fakultas Ekonomi dan Bisnis',
        ]);

        // Dosen
        DB::connection($conn)->table('dosen')->insert([
            [
                'nip' => 'D101',
                'id_regional' => 2,
                'id_prodi' => 'MN',
                'nama_dosen' => 'Dr. Rina Kusuma, M.M.',
                'is_deleted' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nip' => 'D102',
                'id_regional' => 2,
                'id_prodi' => 'AK',
                'nama_dosen' => 'Bambang Sutrisno, S.E., M.Ak.',
                'is_deleted' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // Ruangan
        DB::connection($conn)->table('ruangan')->insert([
            [
                'id_ruangan' => 'GD-B-201',
                'id_regional' => 2,
                'nama_ruangan' => 'Gedung B - Ruang 201',
                'kapasitas' => 40,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_ruangan' => 'GD-B-202',
                'id_regional' => 2,
                'nama_ruangan' => 'Gedung B - Ruang 202',
                'kapasitas' => 35,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // Mata Kuliah
        DB::connection($conn)->table('mata_kuliah')->insert([
            [
                'kode_mk' => 'MN101',
                'kode_mk_prasyarat' => null,
                'id_prodi' => 'MN',
                'nama_mk' => 'Pengantar Manajemen',
                'sks' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode_mk' => 'AK101',
                'kode_mk_prasyarat' => null,
                'id_prodi' => 'AK',
                'nama_mk' => 'Pengantar Akuntansi',
                'sks' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode_mk' => 'MN201',
                'kode_mk_prasyarat' => 'MN101',
                'id_prodi' => 'MN',
                'nama_mk' => 'Manajemen Pemasaran',
                'sks' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // Kelas
        DB::connection($conn)->table('kelas')->insert([
            [
                'id_regional' => 2,
                'kode_mk' => 'MN101',
                'nip_dosen' => 'D101',
                'id_ruangan' => 'GD-B-201',
                'semester' => 1,
                'tahun_akademik' => 2025,
                'kuota' => 40,
                'sisa_kuota' => 38,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_regional' => 2,
                'kode_mk' => 'AK101',
                'nip_dosen' => 'D102',
                'id_ruangan' => 'GD-B-202',
                'semester' => 1,
                'tahun_akademik' => 2025,
                'kuota' => 35,
                'sisa_kuota' => 35,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // Mahasiswa
        DB::connection($conn)->table('mahasiswa')->insert([
            [
                'nim' => '2301001',
                'id_regional' => 2,
                'id_prodi' => 'MN',
                'nama_mahasiswa' => 'Dewi Anggraini',
                'angkatan' => 2023,
                'ips_terakhir' => 3.50,
                'is_deleted' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nim' => '2301002',
                'id_regional' => 2,
                'id_prodi' => 'AK',
                'nama_mahasiswa' => 'Fajar Ramadhan',
                'angkatan' => 2023,
                'ips_terakhir' => 3.20,
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