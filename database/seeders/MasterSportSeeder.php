<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterSportSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('master_sports')->insert([
            ['nama_olahraga' => 'Futsal', 'slug' => 'futsal', 'icon_url' => '/icons/futsal.png', 'status_olahraga' => 'aktif'],
            ['nama_olahraga' => 'Badminton', 'slug' => 'badminton', 'icon_url' => '/icons/badminton.png', 'status_olahraga' => 'aktif'],
            ['nama_olahraga' => 'Mini Soccer', 'slug' => 'mini-soccer', 'icon_url' => '/icons/minisoccer.png', 'status_olahraga' => 'aktif'],
            ['nama_olahraga' => 'Basket', 'slug' => 'basket', 'icon_url' => '/icons/basket.png', 'status_olahraga' => 'aktif'],
        ]);
    }
}