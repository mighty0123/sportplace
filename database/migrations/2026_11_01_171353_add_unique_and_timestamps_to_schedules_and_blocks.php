<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Bersihkan duplikat pada field_schedule jika ada sebelum pasang unique
        $duplicates = DB::table('field_schedule')
            ->select('field_id', 'hari', DB::raw('COUNT(*) as count'))
            ->groupBy('field_id', 'hari')
            ->having('count', '>', 1)
            ->get();

        foreach ($duplicates as $dup) {
            $ids = DB::table('field_schedule')
                ->where('field_id', $dup->field_id)
                ->where('hari', $dup->hari)
                ->pluck('id');

            // Simpan baris pertama, hapus duplikat sisanya
            $idsToDelete = $ids->slice(1);
            if ($idsToDelete->isNotEmpty()) {
                DB::table('field_schedule')->whereIn('id', $idsToDelete)->delete();
            }
        }

        // 2. Tambahkan unique constraint dan timestamps pada field_schedule
        Schema::table('field_schedule', function (Blueprint $table) {
            $table->unique(['field_id', 'hari'], 'field_schedule_field_id_hari_unique');
            if (! Schema::hasColumn('field_schedule', 'created_at')) {
                $table->timestamps();
            }
        });

        // 3. Tambahkan timestamps dan indeks pencarian rentang pada field_blocks
        Schema::table('field_blocks', function (Blueprint $table) {
            if (! Schema::hasColumn('field_blocks', 'created_at')) {
                $table->timestamps();
            }
            $table->index(['field_id', 'tanggal_mulai', 'tanggal_selesai'], 'field_blocks_range_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('field_blocks', function (Blueprint $table) {
            $table->dropIndex('field_blocks_range_index');
            if (Schema::hasColumn('field_blocks', 'created_at')) {
                $table->dropTimestamps();
            }
        });

        Schema::table('field_schedule', function (Blueprint $table) {
            $table->dropUnique('field_schedule_field_id_hari_unique');
            if (Schema::hasColumn('field_schedule', 'created_at')) {
                $table->dropTimestamps();
            }
        });
    }
};
