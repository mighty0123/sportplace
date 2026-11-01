<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    public $timestamps = true;
    protected $table = 'venues';

    protected $fillable = [
        'mitra_id',
        'city_id',
        'nama_venue',
        'alamat_lengkap',
        'deskripsi',
        'map_url',
        'jam_buka',
        'jam_tutup',
        'status_vanue',
        'alasan_penolakan',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'reviewed_at'  => 'datetime',
        ];
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
    
    /**
     * Alias status_venue agar konsisten dengan standar PRD/kode
     */
    public function getStatusVenueAttribute(): ?string
    {
        return $this->attributes['status_vanue'] ?? null;
    }

    public function setStatusVenueAttribute(?string $value): void
    {
        $this->attributes['status_vanue'] = $value;
    }

    public function isDraft(): bool
    {
        return ($this->status_vanue ?? '') === 'draft';
    }

    public function isPendingReview(): bool
    {
        return ($this->status_vanue ?? '') === 'pending_review';
    }

    public function isAktif(): bool
    {
        return ($this->status_vanue ?? '') === 'aktif';
    }

    public function isNonaktif(): bool
    {
        return ($this->status_vanue ?? '') === 'nonaktif';
    }

    public function isRejected(): bool
    {
        return ($this->status_vanue ?? '') === 'rejected';
    }

    /**
     * Format jam operasional ramah pengguna: "08:00 – 23:00 WIB"
     */
    public function getJamOperasionalAttribute(): string
    {
        if (! $this->jam_buka || ! $this->jam_tutup) {
            return '-';
        }
        $buka = substr($this->jam_buka, 0, 5);
        $tutup = substr($this->jam_tutup, 0, 5);

        return "{$buka} – {$tutup} WIB";
    }

    /**
     * Format rentang harga berdasarkan unit lapangan yang dimiliki
     */
    public function getPriceRangeAttribute(): ?string
    {
        if (! $this->relationLoaded('fields')) {
            $this->load('fields');
        }

        $min = $this->fields->min('harga_perjam');
        $max = $this->fields->max('harga_perjam');

        if ($min === null) {
            return null;
        }

        if ((float) $min === (float) $max) {
            return 'Rp ' . number_format($min, 0, ',', '.');
        }

        return 'Rp ' . number_format($min, 0, ',', '.') . ' – Rp ' . number_format($max, 0, ',', '.');
    }

    /**
     * Daftar cabang olahraga yang dinaungi lapangan-lapangan di venue ini
     */
    public function getSportsAttribute()
    {
        if (! $this->relationLoaded('fields.sport')) {
            $this->load('fields.sport');
        }

        return $this->fields->pluck('sport.nama_olahraga')->filter()->unique()->values();
    }

    public function mitra()
    {
        return $this->belongsTo(MitraProfile::class, 'mitra_id');
    }

    public function city()
    {
        return $this->belongsTo(MasterCity::class, 'city_id');
    }

    public function fields()
    {
        return $this->hasMany(Field::class, 'venue_id');
    }
}
