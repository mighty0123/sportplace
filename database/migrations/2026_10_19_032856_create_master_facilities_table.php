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
        Schema::create('master_facilities', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->enum('kategori', ['peralatan', 'keamanan', 'umum']);
            $table->string('icon_url', 255);
            $table->enum('status_fasilitas', ['aktif', 'nonaktif'])->default('aktif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_facilities');
    }
};
