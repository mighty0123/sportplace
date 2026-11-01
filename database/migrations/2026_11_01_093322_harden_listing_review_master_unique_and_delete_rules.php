<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * FK yang diubah dari ON DELETE CASCADE menjadi RESTRICT.
     */
    private const RESTRICTED_FKS = [
        ['venues',           'venues_city_id_foreign',                 'city_id',       'master_cities'],
        ['fields',           'fields_sport_id_foreign',                'sport_id',      'master_sports'],
        ['field_facilities', 'field_facilities_facilities_id_foreign', 'facilities_id', 'master_facilities'],
        ['booking_headers',  'booking_headers_penyewa_id_foreign',     'penyewa_id',    'users'],
        ['booking_details',  'booking_details_field_id_foreign',       'field_id',      'fields'],
        ['refunds',          'refunds_header_id_foreign',              'header_id',     'booking_headers'],
        ['refunds',          'refunds_detail_id_foreign',              'detail_id',     'booking_details'],
        ['refunds',          'refunds_payment_id_foreign',             'payment_id',    'payments'],
        ['refunds',          'refunds_diproses_oleh_foreign',          'diproses_oleh', 'users'],
        ['wallets',          'wallets_user_id_foreign',                'user_id',       'users'],
        ['withdrawals',      'withdrawals_user_id_foreign',            'user_id',       'users'],
    ];

    public function up(): void
    {
        // Semua pemeriksaan di awal: kalau ada masalah, belum ada yang berubah.
        $this->assertNoDuplicates();
        $this->assertForeignKeysExist();

        // Jejak peninjauan listing
        foreach ([['venues', 'status_vanue'], ['fields', 'status_lapangan']] as [$table, $statusColumn]) {
            Schema::table($table, function (Blueprint $t) use ($table, $statusColumn) {
                $t->foreignId('reviewed_by')->nullable()->after('alasan_penolakan')
                    ->constrained('users')->nullOnDelete();
                $t->timestamp('reviewed_at')->nullable()->after('reviewed_by');
                $t->index([$statusColumn, 'submitted_at'], "{$table}_status_submitted_index");
            });
        }

        // Master data tidak boleh ganda.
        Schema::table('master_sports', fn (Blueprint $t) => $t->unique('slug'));
        Schema::table('master_cities', fn (Blueprint $t) => $t->unique(['nama_kota', 'provinsi']));
        Schema::table('master_facilities', fn (Blueprint $t) => $t->unique('nama'));

        // Data yang punya turunan tidak boleh terhapus tanpa sengaja.
        foreach (self::RESTRICTED_FKS as [$table, $name]) {
            Schema::table($table, fn (Blueprint $t) => $t->dropForeign($name));
        }
        foreach (self::RESTRICTED_FKS as [$table, $name, $column, $parent]) {
            Schema::table($table, fn (Blueprint $t) => $t->foreign($column, $name)
                ->references('id')->on($parent)->restrictOnDelete());
        }
    }

    public function down(): void
    {
        foreach (self::RESTRICTED_FKS as [$table, $name]) {
            Schema::table($table, fn (Blueprint $t) => $t->dropForeign($name));
        }
        foreach (self::RESTRICTED_FKS as [$table, $name, $column, $parent]) {
            Schema::table($table, fn (Blueprint $t) => $t->foreign($column, $name)
                ->references('id')->on($parent)->cascadeOnDelete());
        }

        Schema::table('master_facilities', fn (Blueprint $t) => $t->dropUnique(['nama']));
        Schema::table('master_cities', fn (Blueprint $t) => $t->dropUnique(['nama_kota', 'provinsi']));
        Schema::table('master_sports', fn (Blueprint $t) => $t->dropUnique(['slug']));

        foreach (['venues', 'fields'] as $table) {
            Schema::table($table, function (Blueprint $t) use ($table) {
                $t->dropIndex("{$table}_status_submitted_index");
                $t->dropConstrainedForeignId('reviewed_by');
                $t->dropColumn('reviewed_at');
            });
        }
    }

    private function assertNoDuplicates(): void
    {
        $checks = [
            'master_sports.slug' => DB::table('master_sports')
                ->groupBy('slug')->havingRaw('COUNT(*) > 1')->pluck('slug'),
            'master_cities (nama_kota, provinsi)' => DB::table('master_cities')
                ->select('nama_kota', 'provinsi')->groupBy('nama_kota', 'provinsi')->havingRaw('COUNT(*) > 1')
                ->get()->map(fn ($r) => "{$r->nama_kota}, {$r->provinsi}"),
            'master_facilities.nama' => DB::table('master_facilities')
                ->groupBy('nama')->havingRaw('COUNT(*) > 1')->pluck('nama'),
        ];

        $problems = collect($checks)
            ->filter(fn ($rows) => $rows->isNotEmpty())
            ->map(fn ($rows, $label) => "{$label}: " . $rows->implode('; '));

        if ($problems->isNotEmpty()) {
            throw new RuntimeException("Migrasi dibatalkan, ada data ganda. Rapikan dulu:\n" . $problems->implode("\n"));
        }
    }

    private function assertForeignKeysExist(): void
    {
        $missing = [];
        foreach (self::RESTRICTED_FKS as [$table, $name]) {
            if (! in_array($name, array_column(Schema::getForeignKeys($table), 'name'), true)) {
                $missing[] = "{$table}.{$name}";
            }
        }

        if ($missing) {
            throw new RuntimeException('Migrasi dibatalkan, nama foreign key tidak ditemukan: ' . implode(', ', $missing));
        }
    }
};