<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'aktor_role',
        'entitas_type',
        'entitas_id',
        'aksi',
        'status_lama',
        'status_baru',
        'catatan',
        'ip_address',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper untuk mencatat log audit dengan mudah (NFR-LOG-001 / BR-028).
     */
    public static function record(
        ?User $user,
        string $entitasType,
        int $entitasId,
        string $aksi,
        ?string $statusLama = null,
        ?string $statusBaru = null,
        ?string $catatan = null
    ): self {
        return self::create([
            'user_id' => $user?->id,
            'aktor_role' => $user?->role ?? 'system',
            'entitas_type' => $entitasType,
            'entitas_id' => $entitasId,
            'aksi' => $aksi,
            'status_lama' => $statusLama,
            'status_baru' => $statusBaru,
            'catatan' => $catatan,
            'ip_address' => request()->ip(),
            'created_at' => now(),
        ]);
    }
}

