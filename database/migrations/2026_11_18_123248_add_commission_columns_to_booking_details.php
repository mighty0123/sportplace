<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_details', function (Blueprint $t) {
            // Hasil penilaian komisi. NULL berarti belum dinilai (booking lama, atau belum dibayar).
            $t->decimal('komisi_persen', 5, 2)->nullable()->after('harga_slot');
            $t->decimal('komisi_nominal', 15, 2)->nullable()->after('komisi_persen');
            $t->decimal('pendapatan_mitra', 15, 2)->nullable()->after('komisi_nominal');
            // Tarif mana yang dipakai, supaya setiap angka bisa ditelusuri ke riwayat tarif.
            $t->foreignId('komisi_rate_id')->nullable()->after('pendapatan_mitra')->constrained('commission_rates')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('booking_details', function (Blueprint $t) {
            $t->dropConstrainedForeignId('komisi_rate_id');
            $t->dropColumn(['komisi_persen', 'komisi_nominal', 'pendapatan_mitra']);
        });
    }
};