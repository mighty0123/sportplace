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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('header_id')->constrained('booking_headers')->cascadeOnDelete();
            $table->string('pg_transaction_id', 255)->unique();
            $table->string('metode_pembayaran', 50);
            $table->double('nominal_bayar');
            $table->enum('status_pembayaran', ['pending', 'succes', 'failed']);
            $table->dateTime('paid_at')->nullable();
            $table->dateTime('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
