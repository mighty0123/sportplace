<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration penyelesaian GAP-12 (Data usaha, dokumen legalitas KTP/NPWP/NIB)
 * dan GAP-15 (Timestamps) pada tabel mitra_profiles.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('mitra_profiles', function (Blueprint $table) {
            // GAP-12: Data Usaha & Rekening Pencairan
            $table->string('nama_usaha', 150)->nullable()->after('user_id');
            $table->string('bentuk_badan_usaha', 50)->nullable()->after('nama_usaha');
            $table->string('kota_operasional', 100)->nullable()->after('bentuk_badan_usaha');
            $table->text('deskripsi_usaha')->nullable()->after('kota_operasional');
            $table->string('bank_nama', 50)->nullable()->after('deskripsi_usaha');
            $table->string('nomor_rekening', 50)->nullable()->after('bank_nama');
            $table->string('nama_rekening', 100)->nullable()->after('nomor_rekening');

            // GAP-12: Path Berkas Legalitas (Disimpan pada Disk Privat)
            $table->string('file_ktp', 255)->nullable()->after('alasan_penolakan');
            $table->string('file_npwp', 255)->nullable()->after('file_ktp');
            $table->string('file_nib', 255)->nullable()->after('file_npwp');
            $table->string('file_sewa_lahan', 255)->nullable()->after('file_nib');

            // NPWP dijadikan nullable bila opsional (KS-07, F08)
            $table->string('npwp', 24)->nullable()->change();

            // GAP-15: Timestamps
            $table->timestamps();

            // Guard satu profil per user
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mitra_profiles', function (Blueprint $table) {
            $table->dropUnique(['user_id']);
            $table->dropTimestamps();
            $table->dropColumn([
                'nama_usaha',
                'bentuk_badan_usaha',
                'kota_operasional',
                'deskripsi_usaha',
                'bank_nama',
                'nomor_rekening',
                'nama_rekening',
                'file_ktp',
                'file_npwp',
                'file_nib',
                'file_sewa_lahan',
            ]);
            $table->string('npwp', 24)->nullable(false)->change();
        });
    }
};
