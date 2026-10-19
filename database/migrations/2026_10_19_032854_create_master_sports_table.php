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
        Schema::create('master_sports', function (Blueprint $table) {
            $table->id();
            $table->string('nama_olahraga', 30);
            $table->string('slug', 40);
            $table->string('icon_url', 255);
            $table->enum('status_olahraga', ['aktif', 'nonaktif'])->default('aktif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_sports');
    }
};
