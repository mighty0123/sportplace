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
        Schema::create('field_schedule', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_id')->constrained('fields')->cascadeOnDelete();
            $table->tinyInteger('hari');
            $table->time('jam_buka')->nullable();
            $table->time('jam_tutup')->nullable();
            $table->time('durasi')->nullable();
            $table->enum('status_hari', ['buka', 'tutup'])->default('buka');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('field_schedule');
    }
};
