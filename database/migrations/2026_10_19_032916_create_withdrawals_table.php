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
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('bank_kode', 20);
            $table->string('nomor_rekening', 200);
            $table->string('nama_pemilik_rek', 255);
            $table->double('nominal_tarik');
            $table->double('biaya_admin_bank');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->string('pg_disbursement_id', 255)->unique();
            $table->text('alasan_gagal')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('complated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdrawals');
    }
};
