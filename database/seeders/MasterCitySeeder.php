<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterCitySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('master_cities')->insert([
            ['nama_kota' => 'Jakarta Selatan', 'provinsi' => 'DKI Jakarta', 'status_kota' => 'aktif'],
            ['nama_kota' => 'Bandung', 'provinsi' => 'Jawa Barat', 'status_kota' => 'aktif'],
            ['nama_kota' => 'Surabaya', 'provinsi' => 'Jawa Timur', 'status_kota' => 'aktif'],
        ]);
    }
}