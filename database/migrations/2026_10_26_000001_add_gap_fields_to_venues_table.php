<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * GAP-17: Tambah deskripsi nullable pada tabel venues
     * GAP-15: Tambah timestamps nullable pada tabel venues
     */
    public function up(): void
    {
        Schema::table('venues', function (Blueprint $table) {
            if (! Schema::hasColumn('venues', 'deskripsi')) {
                $table->text('deskripsi')->nullable()->after('alamat_lengkap');
            }
            if (! Schema::hasColumn('venues', 'created_at')) {
                $table->nullableTimestamps();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('venues', function (Blueprint $table) {
            if (Schema::hasColumn('venues', 'deskripsi')) {
                $table->dropColumn('deskripsi');
            }
            if (Schema::hasColumn('venues', 'created_at')) {
                $table->dropTimestamps();
            }
        });
    }
};
