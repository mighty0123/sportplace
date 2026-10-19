<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FieldFacilitySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('field_facilities')->insert([
            ['field_id' => 1, 'facilities_id' => 1], // Lapangan A punya Bola
            ['field_id' => 1, 'facilities_id' => 2], // Lapangan A punya Papan Skor
            ['field_id' => 2, 'facilities_id' => 1], // Lapangan B punya Bola
            ['field_id' => 2, 'facilities_id' => 2], // Lapangan B punya Papan Skor
        ]);
    }
}