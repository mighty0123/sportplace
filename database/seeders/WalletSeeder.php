<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WalletSeeder extends Seeder
{
    public function run(): void
    {
        $users = DB::table('users')->get();

        foreach ($users as $user) {
            DB::table('wallets')->updateOrInsert(
                ['user_id' => $user->id],
                [
                    'saldo_aktif' => 0,
                    'saldo_tertahan' => 0,
                    'updated_at' => Carbon::now(),
                ]
            );
        }
    }
}
