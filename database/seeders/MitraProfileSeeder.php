<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MitraProfileSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('mitra_profiles')->insert([
            [
                'user_id' => 2,
                'no_ktp' => '3170000000000001',
                'npwp' => '01.000.000.0-000.000',
                'status' => 'approved',
                'alasan_penolakan' => null,
            ]
        ]);
    }
}