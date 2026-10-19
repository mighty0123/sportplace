<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained('wallets')->cascadeOnDelete();
            $table->enum('jenis_mutasi', ['kredit', 'debit']);
            $table->enum('kategori_transaksi', ['booking_revenue', 'platform_fee_deduction', 'refund_deduction', 'withdrawal']);
            $table->double('nominal');
            $table->enum('referensi_tipe', ['booking_detail', 'withdrawal', 'refund']);
            $table->integer('referensi_id');
            $table->string('deskripsi', 255)->nullable();
            $table->dateTime('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
