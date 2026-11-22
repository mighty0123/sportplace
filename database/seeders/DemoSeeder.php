<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Menjalankan seluruh tahap data simulasi secara berurutan.
 *
 *   php artisan migrate:fresh --seed
 *   php artisan db:seed --class=DemoSeeder 
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            DemoResetSeeder::class,
            DemoAccountSeeder::class,
            DemoMasterDataSeeder::class,
            DemoMitraSeeder::class,
            DemoListingSeeder::class,
            DemoCommissionSeeder::class,
            DemoBookingSeeder::class,
            DemoWalletSeeder::class,
            DemoAuditLogSeeder::class,
        ]);
    }
}
