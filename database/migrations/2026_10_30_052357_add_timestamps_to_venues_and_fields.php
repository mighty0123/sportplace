<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['venues', 'fields'] as $table) {
            Schema::table($table, function (Blueprint $t) use ($table) {
                if (! Schema::hasColumn($table, 'created_at')) {
                    $t->timestamps();
                }
                if (! Schema::hasColumn($table, 'submitted_at')) {
                    $t->timestamp('submitted_at')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        foreach (['venues', 'fields'] as $table) {
            Schema::table($table, fn (Blueprint $t) => $t->dropColumn(['created_at', 'updated_at', 'submitted_at']));
        }
    }
};