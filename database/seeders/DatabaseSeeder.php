<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            MasterCitySeeder::class,
            MasterSportSeeder::class,
            MasterFacilitySeeder::class,
            MitraProfileSeeder::class,
            VenueSeeder::class,
            FieldSeeder::class,
            FieldFacilitySeeder::class,
            FieldScheduleSeeder::class,
            WalletSeeder::class,
        ]);
    }
}