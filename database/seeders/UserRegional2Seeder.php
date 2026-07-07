<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserRegional2Seeder extends Seeder
{
    public function run(): void
    {
        $conn = 'pgsql_r2';
        $now = Carbon::now();

        DB::connection($conn)->table('users')->insert([
            [
                'name' => 'Dewi Anggraini',
                'email' => 'dewi@student.sttc.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'mahasiswa',
                'ref_id' => '2301001',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Fajar Ramadhan',
                'email' => 'fajar@student.sttc.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'mahasiswa',
                'ref_id' => '2301002',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Dr. Rina Kusuma, M.M.',
                'email' => 'rina@dosen.sttc.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'dosen',
                'ref_id' => 'D101',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Admin BAAK Regional 2',
                'email' => 'admin2@baak.sttc.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'baak',
                'ref_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
