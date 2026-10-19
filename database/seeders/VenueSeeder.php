<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VenueSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('venues')->insert([
            [
                'mitra_id' => 1, // ID dari mitra_profiles
                'city_id' => 1,  // ID Jakarta Selatan
                'nama_venue' => 'Champion Futsal Arena',
                'alamat_lengkap' => 'Jl. Sudirman No. 10, Jakarta Selatan',
                'map_url' => 'https://maps.google.com/?q=champion+futsal',
                'jam_buka' => '08:00:00',
                'jam_tutup' => '23:00:00',
                'status_vanue' => 'aktif',
                'alasan_penolakan' => null,
            ]
        ]);
    }
}