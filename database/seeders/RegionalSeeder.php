<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Regional;
use Illuminate\Database\Seeder;

class RegionalSeeder extends Seeder
{
    public function run(): void
    {
        Regional::updateOrCreate(
            ['id_regional' => 1],
            [
                'nama_regional' => 'Kampus Utama / Pusat',
                'lokasi' => 'Jl. Kampus Utama No. 1',
                'fakultas' => 'Fakultas Teknik dan Ilmu Komputer',
            ]
        );
    }
}