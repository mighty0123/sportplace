<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Menyelesaikan:
     * - GAP-20: users.password bertanda UNIQUE di ERD, menolak registrasi bila hash identik. Dihapus.
     * - GAP-09: belum ada kolom status verifikasi OTP. Tambah email_verified_at, phone_verified_at.
     * - GAP-08: belum ada status akun untuk suspend/banned. Tambah status_akun.
     * - GAP-15: belum ada timestamps di users. Tambah created_at/updated_at.
     * - GAP-10: foto_profil belum ada di ERD. Tambah kolom nullable.
     */
    public function up(): void
    {
        try {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique('users_password_unique');
            });
        } catch (\Throwable $e) {
            // Index does not exist or driver cannot drop unique
        }

        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('email_verified_at')->nullable()->after('email');
            $table->timestamp('phone_verified_at')->nullable()->after('no_hp');
            $table->enum('status_akun', ['aktif', 'suspend', 'banned'])->default('aktif')->after('role');
            $table->string('foto_profil')->nullable()->after('status_akun');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'email_verified_at',
                'phone_verified_at',
                'status_akun',
                'foto_profil',
                'created_at',
                'updated_at',
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unique('password');
        });
    }
};
