<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WalletSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('wallets')->insert([
            ['user_id' => 1, 'saldo_aktif' => 0, 'saldo_tertahan' => 0, 'updated_at' => Carbon::now()], // Admin
            ['user_id' => 2, 'saldo_aktif' => 0, 'saldo_tertahan' => 0, 'updated_at' => Carbon::now()], // Mitra
            ['user_id' => 3, 'saldo_aktif' => 0, 'saldo_tertahan' => 0, 'updated_at' => Carbon::now()], // Customer
        ]);
    }
}