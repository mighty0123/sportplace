<?php

namespace App\Services\Admin;

use App\Exceptions\Admin\MitraAlreadyProcessedException;
use App\Models\AuditLog;
use App\Models\MitraProfile;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MitraVerificationService
{
    /** Butir pemeriksaan manual. Satu sumber untuk validasi, tampilan, dan snapshot. */
    public const CHECKLIST = [
        'foto_ktp'      => 'Foto KTP jelas, utuh, dan tidak terlihat diedit',
        'nik_sesuai'    => 'NIK dan nama di KTP sama dengan data formulir',
        'dokumen_usaha' => 'Dokumen usaha yang diunggah terbaca dan sesuai',
    ];

    /** Butir yang berlaku untuk pengajuan ini ("dokumen_usaha" hanya bila ada dokumen usaha). */
    public function checklistItemsFor(MitraProfile $profile): array
    {
        return collect(self::CHECKLIST)
            ->reject(fn ($label, $key) => $key === 'dokumen_usaha' && ! $profile->hasBusinessDocuments())
            ->all();
    }

    public function approve(MitraProfile $profile, User $admin, array $checklist): MitraProfile
    {
        return $this->decide($profile, $admin, 'approved', 'setujui', null, $checklist);
    }

    public function reject(MitraProfile $profile, User $admin, string $reason, array $checklist = []): MitraProfile
    {
        return $this->decide($profile, $admin, 'rejected', 'tolak', $reason, $checklist);
    }

    public function history(MitraProfile $profile): Collection
    {
        return AuditLog::query()
            ->with('user:id,name')
            ->whereIn('entitas_type', ['MitraProfile', MitraProfile::class])
            ->where('entitas_id', $profile->id)
            ->whereNotIn('aksi', ['lihat_dokumen', 'lihat_nik'])
            ->latest('created_at')
            ->latest('id')
            ->get();
    }

    private function decide(MitraProfile $profile, User $admin, string $newStatus, string $aksi, ?string $reason, array $checklist): MitraProfile
    {
        $snapshot = $this->snapshot($profile, $checklist);

        DB::transaction(function () use ($profile, $admin, $newStatus, $aksi, $reason, $snapshot) {
            $affected = MitraProfile::whereKey($profile->getKey())
                ->where('status', 'pending')
                ->update([
                    'status'           => $newStatus,
                    'alasan_penolakan' => $reason,
                    'reviewed_by'      => $admin->id,
                    'reviewed_at'      => now(),
                    'review_checklist' => json_encode($snapshot, JSON_UNESCAPED_UNICODE),
                ]);

            if ($affected === 0) {
                throw new MitraAlreadyProcessedException();
            }

            // Di transaksi yang sama: kalau pencatatan gagal, perubahan status ikut dibatalkan.
            AuditLog::record($admin, 'MitraProfile', $profile->id, $aksi, 'pending', $newStatus, $reason);
        });

        return $profile->refresh();
    }

    /** Foto pemeriksaan saat keputusan dibuat. Hanya butir yang berlaku, nilainya dipaksa boolean. */
    private function snapshot(MitraProfile $profile, array $checklist): array
    {
        $manual = [];
        foreach (array_keys($this->checklistItemsFor($profile)) as $key) {
            $manual[$key] = filter_var($checklist[$key] ?? false, FILTER_VALIDATE_BOOLEAN);
        }

        return ['manual' => $manual];
    }
}