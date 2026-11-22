<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_webhook_events', function (Blueprint $t) {
            $t->id();
            $t->string('provider', 30);
            $t->string('event_id', 120);                       // ID unik dari gateway, dasar penolakan kiriman ganda
            $t->string('pg_transaction_id', 100)->nullable()->index();
            $t->string('event_type', 60)->nullable();
            $t->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();
            $t->boolean('signature_valid')->default(false);
            $t->json('payload');                               // isi mentah; mengandung data pembayar, lihat catatan retensi
            $t->string('process_status', 20)->default('received'); // received, processed, ignored, failed
            $t->text('error')->nullable();
            $t->timestamp('received_at')->useCurrent();
            $t->timestamp('processed_at')->nullable();

            $t->unique(['provider', 'event_id']);
            $t->index(['process_status', 'received_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_webhook_events');
    }
};