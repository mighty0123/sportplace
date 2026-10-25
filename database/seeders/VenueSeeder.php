<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VenueSeeder extends Seeder
{
    public function run(): void
    {
        $mitraApproved = DB::table('users')->where('email', 'mitra@sportplace.com')->first();
        if (! $mitraApproved) {
            return;
        }

        $mitraProfile = DB::table('mitra_profiles')->where('user_id', $mitraApproved->id)->first();
        $city = DB::table('master_cities')->where('nama_kota', 'Jakarta Selatan')->first()
            ?? DB::table('master_cities')->first();

        if ($mitraProfile && $city) {
            DB::table('venues')->updateOrInsert(
                [
                    'mitra_id' => $mitraProfile->id,
                    'nama_venue' => 'Champion Futsal Arena',
                ],
                [
                    'city_id' => $city->id,
                    'alamat_lengkap' => 'Jl. Sudirman No. 10, Jakarta Selatan',
                    'map_url' => 'https://maps.google.com/?q=champion+futsal',
                    'jam_buka' => '08:00:00',
                    'jam_tutup' => '23:00:00',
                    'status_vanue' => 'aktif',
                    'alasan_penolakan' => null,
                ]
            );
        }
    }
}
