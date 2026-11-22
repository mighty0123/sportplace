<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commission_rates', function (Blueprint $t) {
            $t->id();
            // Kunci lingkup: g (global), m12 (mitra 12), s3 (olahraga 3), m12s3 (keduanya). NULL pada mitra_id
            // dan sport_id tidak dianggap sama oleh indeks unik, jadi keunikan dijaga lewat kolom ini.
            $t->string('scope_key', 40);
            $t->foreignId('mitra_id')->nullable()->constrained('mitra_profiles')->restrictOnDelete();
            $t->foreignId('sport_id')->nullable()->constrained('master_sports')->restrictOnDelete();
            // Pada tarif mitra atau olahraga, NULL berarti "ikuti tingkat di atasnya". Tarif global wajib berisi.
            $t->decimal('persen', 5, 2)->nullable();
            $t->dateTime('berlaku_mulai');
            $t->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $t->string('catatan', 255);
            $t->timestamp('created_at')->useCurrent();

            $t->unique(['scope_key', 'berlaku_mulai'], 'commission_rates_scope_time_unique');
            $t->index(['mitra_id', 'berlaku_mulai'], 'commission_rates_mitra_time_index');
        });

        // Tarif awal sesuai keputusan tim. Dengan baris ini, tarif global selalu ada.
        DB::table('commission_rates')->insert([
            'scope_key' => 'g', 'mitra_id' => null, 'sport_id' => null, 'persen' => 20.00,
            'berlaku_mulai' => '2020-01-01 00:00:00', 'dibuat_oleh' => null,
            'catatan' => 'Tarif awal sesuai keputusan tim.', 'created_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_rates');
    }
};