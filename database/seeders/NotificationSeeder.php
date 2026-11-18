<?php

namespace Database\Seeders;

use App\Models\BookingDetail;
use App\Models\User;
use App\Notifications\ListingModerationStatusUpdated;
use App\Notifications\MitraVerificationStatusUpdated;
use App\Notifications\NewBookingReceived;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Seed contoh notifikasi untuk user mitra (T10.1, FR-NOTIF-001, BR-027).
     */
    public function run(): void
    {
        $mitra = User::where('role', 'mitra')->first();

        if (! $mitra) {
            return;
        }

        // 1. Notifikasi Verifikasi Mitra Disetujui
        $mitra->notify(new MitraVerificationStatusUpdated('approved'));

        // 2. Notifikasi Moderasi Lapangan Disetujui
        $mitra->notify(new ListingModerationStatusUpdated('lapangan', 'Lapangan Futsal Standar A', 'aktif'));

        // 3. Notifikasi Pesanan Baru (jika ada data booking)
        $booking = BookingDetail::first();
        if ($booking) {
            $mitra->notify(new NewBookingReceived($booking));
        }
    }
}
