<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Mahasiswa;
use Illuminate\Database\Seeder;

class MahasiswaSeeder extends Seeder
{
    public function run(): void
    {
        $mhs = [
            ['nim' => '2201001', 'id_regional' => 1, 'id_prodi' => 'TI', 'nama_mahasiswa' => 'Luthfi Isa Majid', 'angkatan' => 2022, 'ips_terakhir' => 3.75],
            ['nim' => '2201002', 'id_regional' => 1, 'id_prodi' => 'TI', 'nama_mahasiswa' => 'Rina Amelia', 'angkatan' => 2022, 'ips_terakhir' => 2.50],
            ['nim' => '2201003', 'id_regional' => 1, 'id_prodi' => 'TI', 'nama_mahasiswa' => 'Doni Prasetyo', 'angkatan' => 2022, 'ips_terakhir' => 1.80],
        ];

        foreach ($mhs as $m) {
            Mahasiswa::updateOrCreate(['nim' => $m['nim']], $m);
        }
    }
}
