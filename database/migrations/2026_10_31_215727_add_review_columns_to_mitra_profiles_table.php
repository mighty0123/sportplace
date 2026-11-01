<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mitra_profiles', function (Blueprint $table) {
            $table->timestamp('submitted_at')->nullable()->after('alasan_penolakan');

            $table->foreignId('reviewed_by')->nullable()->after('submitted_at')
                ->constrained('users')->nullOnDelete();

            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');

            $table->json('review_checklist')->nullable()->after('reviewed_at');

            $table->index(['status', 'submitted_at']);
        });

        DB::table('mitra_profiles')->whereNull('submitted_at')->update([
            'submitted_at' => DB::raw(
                "CASE WHEN status = 'pending' THEN COALESCE(updated_at, created_at) ELSE COALESCE(created_at, updated_at) END"
            ),
        ]);
    }

    public function down(): void
    {
        Schema::table('mitra_profiles', function (Blueprint $table) {
            $table->dropIndex(['status', 'submitted_at']);
            $table->dropConstrainedForeignId('reviewed_by');
            $table->dropColumn(['submitted_at', 'reviewed_at', 'review_checklist']);
        });
    }
};