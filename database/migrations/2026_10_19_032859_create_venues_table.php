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
        Schema::create('venues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mitra_id')->constrained('mitra_profiles')->cascadeOnDelete();
            $table->foreignId('city_id')->constrained('master_cities')->cascadeOnDelete();
            $table->string('nama_venue', 200)->nullable();
            $table->text('alamat_lengkap');
            $table->string('map_url', 255)->nullable();
            $table->time('jam_buka');
            $table->time('jam_tutup');
            $table->enum('status_vanue', ['draft', 'pending_review', 'aktif', 'nonaktif', 'rejected'])->default('draft');
            $table->string('alasan_penolakan', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venues');
    }
};
