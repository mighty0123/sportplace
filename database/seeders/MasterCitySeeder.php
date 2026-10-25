<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterCitySeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            ['nama_kota' => 'Jakarta Selatan', 'provinsi' => 'DKI Jakarta', 'status_kota' => 'aktif'],
            ['nama_kota' => 'Bandung', 'provinsi' => 'Jawa Barat', 'status_kota' => 'aktif'],
            ['nama_kota' => 'Surabaya', 'provinsi' => 'Jawa Timur', 'status_kota' => 'aktif'],
        ];

        foreach ($cities as $city) {
            DB::table('master_cities')->updateOrInsert(
                ['nama_kota' => $city['nama_kota']],
                $city
            );
        }
    }
}
