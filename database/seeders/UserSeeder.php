<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin Sportplace',
                'email' => 'admin@sportplace.com',
                'password' => Hash::make('password123'),
                'no_hp' => '08111111111',
                'role' => 'admin',
                'status_akun' => 'aktif',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mitra Approved',
                'email' => 'mitra@sportplace.com',
                'password' => Hash::make('password123'),
                'no_hp' => '08222222222',
                'role' => 'mitra',
                'status_akun' => 'aktif',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mitra Pending',
                'email' => 'mitra_pending@sportplace.com',
                'password' => Hash::make('password123'),
                'no_hp' => '08222222223',
                'role' => 'mitra',
                'status_akun' => 'aktif',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mitra Rejected',
                'email' => 'mitra_rejected@sportplace.com',
                'password' => Hash::make('password123'),
                'no_hp' => '08222222224',
                'role' => 'mitra',
                'status_akun' => 'aktif',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Customer Budi',
                'email' => 'customer@sportplace.com',
                'password' => Hash::make('password123'),
                'no_hp' => '08333333333',
                'role' => 'customer',
                'status_akun' => 'aktif',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Dimas Pratama',
                'email' => 'dimas@sportplace.com',
                'password' => Hash::make('password123'),
                'no_hp' => '08333333334',
                'role' => 'customer',
                'status_akun' => 'aktif',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->updateOrInsert(
                ['email' => $user['email']],
                $user
            );
        }
    }
}