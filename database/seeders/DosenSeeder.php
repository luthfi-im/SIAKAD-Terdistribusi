<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Dosen;
use Illuminate\Database\Seeder;

class DosenSeeder extends Seeder
{
    public function run(): void
    {
        $dosen = [
            ['nip' => 'D001', 'id_regional' => 1, 'id_prodi' => 'TI', 'nama_dosen' => 'Dr. Ahmad Fauzi, M.Kom'],
            ['nip' => 'D002', 'id_regional' => 1, 'id_prodi' => 'TI', 'nama_dosen' => 'Siti Rahayu, M.T'],
            ['nip' => 'D003', 'id_regional' => 1, 'id_prodi' => 'SI', 'nama_dosen' => 'Budi Santoso, M.Kom'],
        ];

        foreach ($dosen as $d) {
            Dosen::updateOrCreate(['nip' => $d['nip']], $d);
        }
    }
}