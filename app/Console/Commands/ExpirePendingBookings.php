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
        // Pembayaran berhasil yang terlambat tidak boleh ditimpa
        $notPaid = fn ($q) => $q->selectRaw('1')->from('payments as p')
            ->whereColumn('p.header_id', 'booking_headers.id')->where('p.status_pembayaran', 'succes');

        $ids = DB::table('booking_headers')
            ->where('status', 'pending')->whereNotNull('expires_at')->where('expires_at', '<=', now())
            ->whereNotExists($notPaid)
            ->orderBy('id')->limit((int) $this->option('limit'))->pluck('id');

        $expired = 0;
        foreach ($ids as $id) {
            DB::transaction(function () use ($id, $notPaid, &$expired) {
                // Pembaruan bersyarat: aman bila webhook sukses tiba di antara pemilihan dan pembaruan
                $n = DB::table('booking_headers')->where('id', $id)->where('status', 'pending')
                    ->whereNotExists($notPaid)->update(['status' => 'expired']);

                if ($n === 1) {
                    AuditLog::record(null, 'BookingHeader', $id, 'kedaluwarsa', 'pending', 'expired', 'Melewati batas waktu bayar.');
                    $expired++;
                }
            });
        }

        $this->info("{$expired} booking dikedaluwarsakan.");

        return self::SUCCESS;
    }
}