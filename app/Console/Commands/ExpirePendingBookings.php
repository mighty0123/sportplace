<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExpirePendingBookings extends Command
{
    protected $signature = 'bookings:expire {--limit=500 : Jumlah maksimal booking yang diproses per jalan}';

    protected $description = 'Ubah booking menunggu pembayaran yang melewati batas bayar menjadi kedaluwarsa (K-03)';

    public function handle(): int
    {
        $window = max(1, (int) config('booking.payment_window_minutes', 15));

        // Pembayaran berhasil yang terlambat tidak boleh ditimpa
        $paid = fn ($q) => $q->selectRaw('1')->from('payments as p')
            ->whereColumn('p.header_id', 'booking_headers.id')->where('p.status_pembayaran', 'succes');

        // Booking tanpa expires_at dihitung dari waktu dibuat
        $overdue = "COALESCE(expires_at, created_at + INTERVAL {$window} MINUTE) <= ?";

        $ids = DB::table('booking_headers')
            ->where('status', 'pending')->whereRaw($overdue, [now()])->whereNotExists($paid)
            ->orderBy('id')->limit((int) $this->option('limit'))->pluck('id');

        $expired = 0;
        foreach ($ids as $id) {
            DB::transaction(function () use ($id, $paid, $overdue, &$expired) {
                // Pembaruan bersyarat: aman bila webhook sukses tiba di antara pemilihan dan pembaruan.
                $n = DB::table('booking_headers')->where('id', $id)->where('status', 'pending')
                    ->whereRaw($overdue, [now()])->whereNotExists($paid)->update(['status' => 'expired']);

                if ($n === 1) {
                    AuditLog::record(null, 'BookingHeader', $id, 'kedaluwarsa', 'pending', 'expired', 'Melewati batas waktu bayar.');
                    // TODO K-01: lepaskan reservasi slot di sini setelah tabel reservasi slot dibuat.
                    $expired++;
                }
            });
        }

        $this->info("{$expired} booking dikedaluwarsakan.");

        return self::SUCCESS;
    }
}