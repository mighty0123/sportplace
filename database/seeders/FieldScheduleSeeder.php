<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FieldScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $schedules = [];
        for ($i = 1; $i <= 7; $i++) {
            $schedules[] = [
                'field_id' => 1,
                'hari' => $i,
                'jam_buka' => '08:00:00',
                'jam_tutup' => '23:00:00',
                'durasi' => '01:00:00', // Slot per 1 jam
                'status_hari' => 'buka',
            ];
        }
        DB::table('field_schedule')->insert($schedules);
    }
}