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
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('header_id')->constrained('booking_headers')->cascadeOnDelete();
            $table->foreignId('detail_id')->constrained('booking_details')->cascadeOnDelete();
            $table->foreignId('payment_id')->constrained('payments')->cascadeOnDelete();
            $table->text('alasan_refund');
            $table->double('nominal_refund');
            $table->string('metode_refund', 50);
            $table->string('rekening_tujuan', 200);
            $table->enum('status_refund', ['pending', 'processing', 'completed', 'rejected'])->default('pending');
            $table->foreignId('diproses_oleh')->nullable()->constrained('users')->cascadeOnDelete();
            $table->dateTime('complated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};
