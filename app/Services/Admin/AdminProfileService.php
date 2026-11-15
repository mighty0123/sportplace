<?php

namespace App\Services\Admin;

use App\Models\AuditLog;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/** Profil dan keamanan akun admin */
class AdminProfileService
{
    public function sessions(User $user, string $currentId): Collection
    {
        if (config('session.driver') !== 'database') {
            return collect();
        }

        // ID sesi tidak pernah dikirim ke tampilan
        return DB::table(config('session.table', 'sessions'))->where('user_id', $user->id)
            ->orderByDesc('last_activity')->limit(20)->get(['id', 'ip_address', 'user_agent', 'last_activity'])
            ->map(fn ($s) => (object) [
                'current' => hash_equals($currentId, (string) $s->id),
                'device'  => self::describeAgent((string) $s->user_agent),
                'ip'      => $s->ip_address,
                'last'    => CarbonImmutable::createFromTimestamp((int) $s->last_activity),
            ]);
    }

    public function recentActivity(User $user, int $limit = 10): Collection
    {
        return AuditLog::query()->where('user_id', $user->id)->latest('created_at')->latest('id')->limit($limit)->get();
    }

    /** @return string[] yang berubah (kosong bila tidak ada perubahan) */
    public function updateProfile(User $user, string $name, string $phone): array
    {
        return DB::transaction(function () use ($user, $name, $phone) {
            $row = DB::table('users')->where('id', $user->id)->lockForUpdate()->first();

            $changes = [];
            $labels = [];
            if ($row->name !== $name) {
                $changes['name'] = $name;
                $labels[] = 'nama';
            }
            if ($row->no_hp !== $phone) {
                $changes['no_hp'] = $phone;
                $changes['phone_verified_at'] = null; // nomor baru belum terverifikasi
                $labels[] = 'nomor HP (verifikasi diulang)';
            }
            if ($changes === []) {
                return [];
            }

            DB::table('users')->where('id', $user->id)->update($changes + ['updated_at' => now()]);
            AuditLog::record($user, 'User', $user->id, 'ubah_profil', null, null, 'Mengubah ' . implode(', ', $labels) . '.');

            return $labels;
        });
    }

    /** @return int jumlah perangkat lain yang dikeluarkan */
    public function changePassword(User $user, string $newPassword, string $currentSessionId): int
    {
        return DB::transaction(function () use ($user, $newPassword, $currentSessionId) {
            // Langsung ke tabel: aman walau model sudah memakai cast "hashed" (tidak ada hash ganda).
            DB::table('users')->where('id', $user->id)->update([
                'password' => Hash::make($newPassword), 'remember_token' => Str::random(60), 'updated_at' => now(),
            ]);

            $out = $this->deleteOtherSessions($user->id, $currentSessionId);
            AuditLog::record($user, 'User', $user->id, 'ganti_password', null, null,
                'Mengganti kata sandi' . ($out ? " dan mengeluarkan {$out} perangkat lain." : '.'));

            return $out;
        });
    }

    public function logoutOthers(User $user, string $currentSessionId): int
    {
        $out = $this->deleteOtherSessions($user->id, $currentSessionId);
        AuditLog::record($user, 'User', $user->id, 'keluar_perangkat_lain', null, null, "Mengeluarkan {$out} perangkat lain.");

        return $out;
    }

    private function deleteOtherSessions(int $userId, string $currentId): int
    {
        if (config('session.driver') !== 'database') {
            return 0;
        }

        return DB::table(config('session.table', 'sessions'))->where('user_id', $userId)->where('id', '!=', $currentId)->delete();
    }

    /** Ringkasan perangkat dari User-Agent: "Chrome di Windows". Urutan pencocokan penting (Android memuat "Linux"). */
    public static function describeAgent(string $ua): string
    {
        $browser = match (true) {
            str_contains($ua, 'Edg/')                              => 'Edge',
            str_contains($ua, 'OPR/')                              => 'Opera',
            str_contains($ua, 'Firefox/')                          => 'Firefox',
            str_contains($ua, 'Chrome/')                           => 'Chrome',
            str_contains($ua, 'Safari/')                           => 'Safari',
            default                                                => 'Peramban tidak dikenal',
        };
        $os = match (true) {
            str_contains($ua, 'Windows')                           => 'Windows',
            str_contains($ua, 'Android')                           => 'Android',
            str_contains($ua, 'iPhone'), str_contains($ua, 'iPad') => 'iOS',
            str_contains($ua, 'Mac OS X')                          => 'macOS',
            str_contains($ua, 'Linux')                             => 'Linux',
            default                                                => 'sistem tidak dikenal',
        };

        return "{$browser} di {$os}";
    }
}