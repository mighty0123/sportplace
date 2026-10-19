<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FieldSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('fields')->insert([
            [
                'venue_id' => 1,
                'sport_id' => 1, // Futsal
                'nama_lapangan' => 'Lapangan Vinyl A',
                'harga_perjam' => 150000,
                'meta_harga' => json_encode(['siang' => 100000, 'malam' => 150000]),
                'status_lapangan' => 'aktif',
                'alasan_penolakan' => null,
            ],
            [
                'venue_id' => 1,
                'sport_id' => 1, // Futsal
                'nama_lapangan' => 'Lapangan Sintetis B',
                'harga_perjam' => 180000,
                'meta_harga' => json_encode(['siang' => 120000, 'malam' => 180000]),
                'status_lapangan' => 'aktif',
                'alasan_penolakan' => null,
            ]
        ]);
    }
}