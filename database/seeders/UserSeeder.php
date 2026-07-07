<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Luthfi Isa Majid', 'email' => 'luthfi@student.sttc.ac.id', 'role' => 'mahasiswa', 'ref_id' => '2201001'],
            ['name' => 'Siti Rahayu', 'email' => 'siti@dosen.sttc.ac.id', 'role' => 'dosen', 'ref_id' => 'D002'],
            ['name' => 'Admin BAAK', 'email' => 'admin@baak.sttc.ac.id', 'role' => 'baak', 'ref_id' => null],
        ];

        foreach ($users as $u) {
            User::updateOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'password' => Hash::make('password123'), // password sama utk semua, testing only
                    'role' => $u['role'],
                    'ref_id' => $u['ref_id'],
                ]
            );
        }
    }
}