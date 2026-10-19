<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Admin Sportplace',
                'email' => 'admin@sportplace.com',
                'password' => Hash::make('password123'),
                'no_hp' => '08111111111',
                'role' => 'admin'
            ],
            [
                'name' => 'Mitra Futsal',
                'email' => 'mitra@sportplace.com',
                'password' => Hash::make('password123'),
                'no_hp' => '08222222222',
                'role' => 'mitra'
            ],
            [
                'name' => 'Customer Budi',
                'email' => 'customer@sportplace.com',
                'password' => Hash::make('password123'),
                'no_hp' => '08333333333',
                'role' => 'customer'
            ]
        ]);
    }
}