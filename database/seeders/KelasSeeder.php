<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Kelas;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $kelas = [
            ['id_regional' => 1, 'kode_mk' => 'TI101', 'nip_dosen' => 'D001', 'id_ruangan' => 'GD-A-101', 'semester' => 1, 'tahun_akademik' => 2025, 'kuota' => 3, 'sisa_kuota' => 3],
            ['id_regional' => 1, 'kode_mk' => 'TI201', 'nip_dosen' => 'D002', 'id_ruangan' => 'LAB-KOM-1', 'semester' => 1, 'tahun_akademik' => 2025, 'kuota' => 2, 'sisa_kuota' => 2],
            ['id_regional' => 1, 'kode_mk' => 'TI302', 'nip_dosen' => 'D003', 'id_ruangan' => 'GD-A-102', 'semester' => 1, 'tahun_akademik' => 2025, 'kuota' => 5, 'sisa_kuota' => 5],
        ];

        foreach ($kelas as $k) {
            Kelas::updateOrCreate(
                ['kode_mk' => $k['kode_mk'], 'tahun_akademik' => $k['tahun_akademik'], 'id_regional' => $k['id_regional']],
                $k
            );
        }
    }
}