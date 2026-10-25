<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FieldScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $fields = DB::table('fields')->get();

        foreach ($fields as $field) {
            for ($i = 1; $i <= 7; $i++) {
                DB::table('field_schedule')->updateOrInsert(
                    [
                        'field_id' => $field->id,
                        'hari' => $i,
                    ],
                    [
                        'jam_buka' => '08:00:00',
                        'jam_tutup' => '23:00:00',
                        'durasi' => '01:00:00',
                        'status_hari' => 'buka',
                    ]
                );
            }
        }
    }
}
