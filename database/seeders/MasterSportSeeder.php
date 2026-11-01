<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterSportSeeder extends Seeder
{
    public function run(): void
    {
        $sports = [
            ['nama_olahraga' => 'Futsal', 'slug' => 'futsal', 'icon_url' => '/icons/futsal.png', 'status_olahraga' => 'aktif'],
            ['nama_olahraga' => 'Badminton', 'slug' => 'badminton', 'icon_url' => '/icons/badminton.png', 'status_olahraga' => 'aktif'],
            ['nama_olahraga' => 'Mini Soccer', 'slug' => 'mini-soccer', 'icon_url' => '/icons/minisoccer.png', 'status_olahraga' => 'aktif'],
            ['nama_olahraga' => 'Basket', 'slug' => 'basket', 'icon_url' => '/icons/basket.png', 'status_olahraga' => 'aktif'],
            ['nama_olahraga' => 'Tenis', 'slug' => 'tenis', 'icon_url' => '/icons/tennis.png', 'status_olahraga' => 'aktif'],
            ['nama_olahraga' => 'Voli', 'slug' => 'voli', 'icon_url' => '/icons/volleyball.png', 'status_olahraga' => 'aktif'],
            ['nama_olahraga' => 'Tenis Meja', 'slug' => 'tenis-meja', 'icon_url' => '/icons/table-tennis.png', 'status_olahraga' => 'aktif'],
        ];

        foreach ($sports as $sport) {
            DB::table('master_sports')->updateOrInsert(
                ['slug' => $sport['slug']],
                $sport
            );
        }
    }
}
