<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\KalenderAkademik;
use Illuminate\Database\Seeder;

class KalenderAkademikSeeder extends Seeder
{
    public function run(): void
    {
        KalenderAkademik::updateOrCreate(
            ['semester' => 'Ganjil', 'tahun_ajaran' => '2025/2026'],
            ['status_aktif' => true] // KRS lagi dibuka
        );
    }
}
