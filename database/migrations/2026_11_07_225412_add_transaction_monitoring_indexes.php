<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_headers', function (Blueprint $t) {
            $t->index('created_at', 'booking_headers_created_index');
            $t->index(['status', 'created_at'], 'booking_headers_status_created_index');
        });
        Schema::table('payments', function (Blueprint $t) {
            $t->index(['status_pembayaran', 'paid_at'], 'payments_status_paid_index');
            $t->index(['header_id', 'status_pembayaran'], 'payments_header_status_index'); // mempercepat penanda per booking
        });
        Schema::table('booking_details', fn (Blueprint $t) => $t->index(['field_id', 'tanggal_sewa'], 'booking_details_field_date_index'));
        Schema::table('refunds', fn (Blueprint $t) => $t->index(['header_id', 'status_refund'], 'refunds_header_status_index'));
    }

    public function down(): void
    {
        Schema::table('refunds', fn (Blueprint $t) => $t->dropIndex('refunds_header_status_index'));
        Schema::table('booking_details', fn (Blueprint $t) => $t->dropIndex('booking_details_field_date_index'));
        Schema::table('payments', function (Blueprint $t) {
            $t->dropIndex('payments_header_status_index');
            $t->dropIndex('payments_status_paid_index');
        });
        Schema::table('booking_headers', function (Blueprint $t) {
            $t->dropIndex('booking_headers_status_created_index');
            $t->dropIndex('booking_headers_created_index');
        });
    }
};