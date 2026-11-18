<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slot_reservations', function (Blueprint $t) {
            $t->id();
            $t->foreignId('header_id')->constrained('booking_headers')->cascadeOnDelete();
            $t->foreignId('detail_id')->nullable()->constrained('booking_details')->cascadeOnDelete();
            $t->foreignId('field_id')->constrained('fields')->restrictOnDelete();
            $t->date('slot_date');
            $t->time('slot_start');
            $t->unsignedTinyInteger('holds')->nullable()->default(1); // 1 = dipegang, NULL = dilepas (riwayat)
            $t->timestamp('released_at')->nullable();
            $t->timestamp('created_at')->useCurrent();

            $t->unique(['field_id', 'slot_date', 'slot_start', 'holds'], 'slot_reservations_active_unique');
            $t->index(['header_id', 'holds'], 'slot_reservations_header_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slot_reservations');
    }
};