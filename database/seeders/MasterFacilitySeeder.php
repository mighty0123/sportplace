<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterFacilitySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('master_facilities')->insert([
            ['nama' => 'Bola / Shuttlecock', 'kategori' => 'peralatan', 'icon_url' => '/icons/ball.png', 'status_fasilitas' => 'aktif'],
            ['nama' => 'Papan Skor', 'kategori' => 'peralatan', 'icon_url' => '/icons/score.png', 'status_fasilitas' => 'aktif'],
            ['nama' => 'Kamera CCTV', 'kategori' => 'keamanan', 'icon_url' => '/icons/cctv.png', 'status_fasilitas' => 'aktif'],
            ['nama' => 'Kantin', 'kategori' => 'umum', 'icon_url' => '/icons/canteen.png', 'status_fasilitas' => 'aktif'],
            ['nama' => 'Toilet & Shower', 'kategori' => 'umum', 'icon_url' => '/icons/toilet.png', 'status_fasilitas' => 'aktif'],
            ['nama' => 'Parkir Luas', 'kategori' => 'umum', 'icon_url' => '/icons/parking.png', 'status_fasilitas' => 'aktif'],
        ]);
    }
}