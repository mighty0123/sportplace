<?php

namespace App\Services\Admin;

use App\Exceptions\Admin\MitraAlreadyProcessedException;
use App\Exceptions\Admin\MitraBlockedByChecksException;
use App\Models\AuditLog;
use App\Models\MitraProfile;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Keputusan admin atas pendaftaran mitra. Satu-satunya tempat yang mengubah
 * status pendaftaran, jadi aturan tidak tersebar di controller.
 */
class MitraVerificationService
{
    /** Butir pemeriksaan manual. Satu sumber untuk validasi, tampilan, dan snapshot. */
    public const CHECKLIST = [
        'foto_ktp'      => 'Foto KTP jelas, utuh, dan tidak terlihat diedit',
        'nik_sesuai'    => 'NIK dan nama di KTP sama dengan data formulir',
        'dokumen_usaha' => 'Dokumen usaha yang diunggah terbaca dan sesuai',
    ];

    public function __construct(private MitraDataChecks $checks)
    {
    }

    /** Butir manual yang berlaku untuk pengajuan ini ("dokumen_usaha" hanya bila ada dokumen usaha). */
    public function checklistItemsFor(MitraProfile $profile): array
    {
        return collect(self::CHECKLIST)
            ->reject(fn ($label, $key) => $key === 'dokumen_usaha' && ! $profile->hasBusinessDocuments())
            ->all();
    }

    public function approve(MitraProfile $profile, User $admin, array $checklist): MitraProfile
    {
        // Dihitung ulang di server saat keputusan
        $results = $this->checks->run($profile);

        if ($this->checks->isBlocked($results)) {
            throw new MitraBlockedByChecksException($this->checks->blockingMessage($results));
        }

        return $this->decide($profile, $admin, 'approved', 'setujui', null, $checklist, $results);
    }

    public function reject(MitraProfile $profile, User $admin, string $reason, array $checklist = []): MitraProfile
    {
        // Menolak tidak pernah diblokir pemeriksaan; hasilnya tetap dicatat sebagai bukti.
        return $this->decide($profile, $admin, 'rejected', 'tolak', $reason, $checklist, $this->checks->run($profile));
    }

    /** Riwayat pendaftaran */
    public function history(MitraProfile $profile): Collection
    {
        return AuditLog::query()
            ->with('user:id,name')
            // Dua bentuk nama entitas ada di data: nama pendek (helper aplikasi) dan nama class lengkap.
            ->whereIn('entitas_type', ['MitraProfile', MitraProfile::class])
            ->where('entitas_id', $profile->id)
            ->whereNotIn('aksi', ['lihat_dokumen', 'lihat_nik', 'lihat_data'])
            ->latest('created_at')
            ->latest('id')
            ->get();
    }

    private function decide(MitraProfile $profile, User $admin, string $newStatus, string $aksi, ?string $reason, array $checklist, array $results): MitraProfile
    {
        $snapshot = $this->snapshot($profile, $checklist, $results);

        DB::transaction(function () use ($profile, $admin, $newStatus, $aksi, $reason, $snapshot) {
            // Update bersyarat: hanya berhasil kalau status masih 'pending' saat query dijalankan.
            // json_encode manual: update massal lewat query builder tidak menerapkan cast.
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

    /** Foto pemeriksaan saat keputusan dibuat: centang manual dan hasil otomatis. */
    private function snapshot(MitraProfile $profile, array $checklist, array $results): array
    {
        $manual = null;
        if ($checklist !== []) {
            $manual = [];
            foreach (array_keys($this->checklistItemsFor($profile)) as $key) {
                $manual[$key] = filter_var($checklist[$key] ?? false, FILTER_VALIDATE_BOOLEAN);
            }
        }

        return [
            'manual'  => $manual,
            'auto'    => $this->checks->snapshot($results),
            'auto_at' => now()->toIso8601String(),
        ];
    }
}