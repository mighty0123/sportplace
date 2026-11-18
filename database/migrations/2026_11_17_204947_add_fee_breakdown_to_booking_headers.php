<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_headers', function (Blueprint $t) {
            // Kosong berarti "tidak tercatat" (booking lama). Booking baru wajib mengisinya.
            $t->decimal('subtotal_slot', 15, 2)->nullable()->after('total_tagihan');
            $t->decimal('biaya_layanan', 15, 2)->nullable()->after('subtotal_slot');
        });
        DB::statement(<<<'SQL'
            UPDATE booking_headers h
            JOIN (SELECT header_id, SUM(harga_slot) AS s FROM booking_details GROUP BY header_id) d ON d.header_id = h.id
            SET h.subtotal_slot = ROUND(d.s, 2),
                h.biaya_layanan = IF(h.total_tagihan >= d.s, ROUND(h.total_tagihan - d.s, 2), NULL)
        SQL);
    }

    public function down(): void
    {
        Schema::table('booking_headers', fn (Blueprint $t) => $t->dropColumn(['subtotal_slot', 'biaya_layanan']));
    }
};