<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Services\Booking\SlotReservationService;

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
                    app(SlotReservationService::class)->release($id);
                    $expired++;
                }
            });
        }

        $this->info("{$expired} booking dikedaluwarsakan.");

        return self::SUCCESS;
    }

    public function test_expiring_a_booking_releases_its_slot(): void
    {
        $id = $this->booking($this->makeUser(), ['created_at' => now()->subHour(), 'expires_at' => now()->subMinutes(45)]);
        [$city, $sport] = $this->cityAndSport();
        $field = $this->field($this->venue($this->mitraProfile($this->makeUser('mitra'), ['status' => 'approved']), $city, ['status_vanue' => 'aktif']), $sport);
        $this->bookingDetail($id, $field, ['tanggal_sewa' => now()->addDays(3)->format('Y-m-d') . ' 00:00:00', 'jam_mulai' => '19:00:00', 'jam_selesai' => '20:00:00']);
        app(\App\Services\Booking\SlotReservationService::class)->reserve($id);

        $this->artisan('bookings:expire')->assertSuccessful();

        $this->assertSame(0, \Illuminate\Support\Facades\DB::table('slot_reservations')->where('header_id', $id)->where('holds', 1)->count());
    }
}