<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * GAP-17: Tambah deskripsi nullable pada tabel fields.
     * Spesifikasi fisik lapangan sesuai desain Figma: tipe_bangunan, jenis_permukaan, panjang, lebar, kapasitas, aturan_lapangan.
     */
    public function up(): void
    {
        Schema::table('fields', function (Blueprint $table) {
            if (! Schema::hasColumn('fields', 'deskripsi')) {
                $table->text('deskripsi')->nullable()->after('harga_perjam');
            }
            if (! Schema::hasColumn('fields', 'tipe_bangunan')) {
                $table->string('tipe_bangunan', 50)->nullable()->default('indoor')->after('deskripsi');
            }
            if (! Schema::hasColumn('fields', 'jenis_permukaan')) {
                $table->string('jenis_permukaan', 100)->nullable()->after('tipe_bangunan');
            }
            if (! Schema::hasColumn('fields', 'panjang')) {
                $table->decimal('panjang', 5, 2)->nullable()->after('jenis_permukaan');
            }
            if (! Schema::hasColumn('fields', 'lebar')) {
                $table->decimal('lebar', 5, 2)->nullable()->after('panjang');
            }
            if (! Schema::hasColumn('fields', 'kapasitas')) {
                $table->string('kapasitas', 50)->nullable()->after('lebar');
            }
            if (! Schema::hasColumn('fields', 'aturan_lapangan')) {
                $table->text('aturan_lapangan')->nullable()->after('kapasitas');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fields', function (Blueprint $table) {
            $table->dropColumn([
                'deskripsi',
                'tipe_bangunan',
                'jenis_permukaan',
                'panjang',
                'lebar',
                'kapasitas',
                'aturan_lapangan',
            ]);
        });
    }
};
