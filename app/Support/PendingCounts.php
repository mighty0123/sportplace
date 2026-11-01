<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * Jumlah antrian yang menunggu tindakan admin. Dipakai badge di sidebar.
 * Key harus sama dengan 'key' di config/admin.php.
 */
class PendingCounts
{
    public static function all(): array
    {
        return [
            'verifikasi' => DB::table('mitra_profiles')
                ->where('status', 'pending')
                ->count(),

            'moderasi' => DB::table('venues')->where('status_vanue', 'pending_review')->count()
                + DB::table('fields')->where('status_lapangan', 'pending_review')->count(),
        ];
    }
}