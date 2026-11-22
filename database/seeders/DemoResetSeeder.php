<?php

namespace Database\Seeders;

use DateTimeImmutable;
use DateTimeZone;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/** Mengosongkan tabel listing dan transaksi lalu menyiapkan keadaan bersama untuk seluruh tahap. */
class DemoResetSeeder extends DemoBase
{
    public function run(): void
    {
        $this->begin();
        $this->wipe();
    }

    private function wipe(): void
    {
        Schema::disableForeignKeyConstraints();
        foreach ([
            'payment_webhook_events', 'refunds', 'wallet_transactions', 'withdrawals', 'slot_reservations',
            'payments', 'booking_details', 'booking_headers', 'field_blocks', 'field_photo', 'field_facilities',
            'field_schedule', 'fields', 'venues', 'audit_logs', 'commission_rates',
        ] as $table) {
            DB::table($table)->truncate();
        }
        Schema::enableForeignKeyConstraints();
    }
}
