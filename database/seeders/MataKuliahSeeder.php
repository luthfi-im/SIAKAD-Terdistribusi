<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\MataKuliah;
use Illuminate\Database\Seeder;

class MataKuliahSeeder extends Seeder
{
    public function run(): void
    {
        $mk = [
            ['kode_mk' => 'TI101', 'kode_mk_prasyarat' => null, 'id_prodi' => 'TI', 'nama_mk' => 'Algoritma dan Pemrograman', 'sks' => 3],
            ['kode_mk' => 'TI201', 'kode_mk_prasyarat' => 'TI101', 'id_prodi' => 'TI', 'nama_mk' => 'Struktur Data', 'sks' => 3],
            ['kode_mk' => 'TI301', 'kode_mk_prasyarat' => 'TI201', 'id_prodi' => 'TI', 'nama_mk' => 'Basis Data Terdistribusi', 'sks' => 3],
            ['kode_mk' => 'TI302', 'kode_mk_prasyarat' => null, 'id_prodi' => 'TI', 'nama_mk' => 'Matematika Diskrit', 'sks' => 2],
        ];

        // Insert dulu tanpa prasyarat biar gak bentrok FK self-reference
        foreach ($mk as $m) {
            MataKuliah::updateOrCreate(['kode_mk' => $m['kode_mk']], [
                'kode_mk_prasyarat' => null,
                'id_prodi' => $m['id_prodi'],
                'nama_mk' => $m['nama_mk'],
                'sks' => $m['sks'],
            ]);
        }
        // Baru update prasyaratnya
        foreach ($mk as $m) {
            if ($m['kode_mk_prasyarat']) {
                MataKuliah::where('kode_mk', $m['kode_mk'])->update(['kode_mk_prasyarat' => $m['kode_mk_prasyarat']]);
            }
        }
    }
}