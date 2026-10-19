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
        Schema::create('fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venue_id')->constrained('venues')->cascadeOnDelete();
            $table->foreignId('sport_id')->constrained('master_sports')->cascadeOnDelete();
            $table->string('nama_lapangan', 100);
            $table->double('harga_perjam');
            $table->json('meta_harga')->nullable();
            $table->enum('status_lapangan', ['draft', 'pending_review', 'aktif', 'nonaktif', 'rejected'])->default('draft');
            $table->string('alasan_penolakan', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fields');
    }
};
