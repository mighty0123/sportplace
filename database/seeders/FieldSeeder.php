<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FieldSeeder extends Seeder
{
    public function run(): void
    {
        $venue = DB::table('venues')->first();
        $sport = DB::table('master_sports')->where('slug', 'futsal')->first()
            ?? DB::table('master_sports')->first();

        if (! $venue || ! $sport) {
            return;
        }

        $fields = [
            [
                'nama_lapangan' => 'Lapangan Vinyl A',
                'harga_perjam' => 150000,
                'meta_harga' => json_encode(['siang' => 100000, 'malam' => 150000]),
                'status_lapangan' => 'aktif',
                'alasan_penolakan' => null,
            ],
            [
                'nama_lapangan' => 'Lapangan Sintetis B',
                'harga_perjam' => 180000,
                'meta_harga' => json_encode(['siang' => 120000, 'malam' => 180000]),
                'status_lapangan' => 'aktif',
                'alasan_penolakan' => null,
            ],
        ];

        foreach ($fields as $field) {
            DB::table('fields')->updateOrInsert(
                [
                    'venue_id' => $venue->id,
                    'nama_lapangan' => $field['nama_lapangan'],
                ],
                [
                    'sport_id' => $sport->id,
                    'harga_perjam' => $field['harga_perjam'],
                    'meta_harga' => $field['meta_harga'],
                    'status_lapangan' => $field['status_lapangan'],
                    'alasan_penolakan' => $field['alasan_penolakan'],
                ]
            );
        }
    }
}
