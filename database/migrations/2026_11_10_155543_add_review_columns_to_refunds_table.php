<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('refunds', function (Blueprint $t) {
            // Refund lama tidak punya waktu pengajuan; dibiarkan kosong, tidak dikarang
            $t->timestamp('created_at')->nullable()->after('complated_at');
            $t->timestamp('updated_at')->nullable()->after('created_at');
            $t->string('alasan_penolakan', 255)->nullable()->after('alasan_refund');
            $t->string('gateway_refund_id', 100)->nullable()->after('metode_refund');

            $t->unique('gateway_refund_id', 'refunds_gateway_refund_id_unique');
            $t->index(['status_refund', 'created_at'], 'refunds_status_created_index');
        });
    }

    public function down(): void
    {
        Schema::table('refunds', function (Blueprint $t) {
            $t->dropIndex('refunds_status_created_index');
            $t->dropUnique('refunds_gateway_refund_id_unique');
            $t->dropColumn(['created_at', 'updated_at', 'alasan_penolakan', 'gateway_refund_id']);
        });
    }
};