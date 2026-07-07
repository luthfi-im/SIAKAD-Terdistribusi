<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RegionalSeeder::class,
            DosenSeeder::class,
            MataKuliahSeeder::class,
            RuanganSeeder::class,
            MahasiswaSeeder::class,
            KelasSeeder::class,
            KalenderAkademikSeeder::class,
            UserSeeder::class,
        ]);
    }
}
