<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Tanpa huruf dan angka yang mudah tertukar: I, L, O, 0, 1.
    private const ALPHABET = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';

    public function up(): void
    {
        Schema::table('booking_headers', function (Blueprint $t) {
            $t->string('kode_booking', 12)->nullable()->after('id');
            $t->dateTime('expires_at')->nullable()->after('status');
        });

        // Booking lama diberi kode acak yang unik.
        DB::table('booking_headers')->whereNull('kode_booking')->orderBy('id')->chunkById(500, function ($rows) {
            foreach ($rows as $row) {
                do {
                    $code = $this->makeCode();
                } while (DB::table('booking_headers')->where('kode_booking', $code)->exists());

                DB::table('booking_headers')->where('id', $row->id)->update(['kode_booking' => $code]);
            }
        });

        // Hanya yang masih menunggu pembayaran yang punya batas bayar (15 menit sejak dibuat)
        $rawExpr = DB::getDriverName() === 'sqlite'
            ? "datetime(created_at, '+15 minutes')"
            : 'created_at + INTERVAL 15 MINUTE';
        DB::table('booking_headers')->where('status', 'pending')
            ->update(['expires_at' => DB::raw($rawExpr)]);

        Schema::table('booking_headers', function (Blueprint $t) {
            $t->unique('kode_booking', 'booking_headers_kode_booking_unique');
            // Untuk pencarian penjadwal: status 'pending' yang sudah lewat batas
            $t->index(['status', 'expires_at'], 'booking_headers_status_expires_index');
        });
    }

    public function down(): void
    {
        Schema::table('booking_headers', function (Blueprint $t) {
            $t->dropIndex('booking_headers_status_expires_index');
            $t->dropUnique('booking_headers_kode_booking_unique');
            $t->dropColumn(['kode_booking', 'expires_at']);
        });
    }

    private function makeCode(): string
    {
        $s = '';
        for ($i = 0; $i < 6; $i++) {
            $s .= self::ALPHABET[random_int(0, strlen(self::ALPHABET) - 1)];
        }

        return 'SP-' . $s;
    }
};