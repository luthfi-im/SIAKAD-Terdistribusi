<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Ruangan;
use Illuminate\Database\Seeder;

class RuanganSeeder extends Seeder
{
    public function run(): void
    {
        $ruangan = [
            ['id_ruangan' => 'GD-A-101', 'id_regional' => 1, 'nama_ruangan' => 'Gedung A - Ruang 101', 'kapasitas' => 40],
            ['id_ruangan' => 'GD-A-102', 'id_regional' => 1, 'nama_ruangan' => 'Gedung A - Ruang 102', 'kapasitas' => 35],
            ['id_ruangan' => 'LAB-KOM-1', 'id_regional' => 1, 'nama_ruangan' => 'Lab Komputer 1', 'kapasitas' => 30],
        ];

        foreach ($ruangan as $r) {
            Ruangan::updateOrCreate(['id_ruangan' => $r['id_ruangan']], $r);
        }
    }
}
