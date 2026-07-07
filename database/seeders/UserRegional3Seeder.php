<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserRegional3Seeder extends Seeder
{
    public function run(): void
    {
        $conn = 'pgsql_r3';
        $now = Carbon::now();

        DB::connection($conn)->table('users')->insert([
            [
                'name' => 'Amelia Putri Lestari',
                'email' => 'amelia@student.sttc.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'mahasiswa',
                'ref_id' => '2401001',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Rizky Aditya Nugraha',
                'email' => 'rizky@student.sttc.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'mahasiswa',
                'ref_id' => '2401002',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'dr. Anisa Putri, Sp.PD',
                'email' => 'anisa@dosen.sttc.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'dosen',
                'ref_id' => 'D201',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Admin BAAK Regional 3',
                'email' => 'admin3@baak.sttc.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'baak',
                'ref_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
