<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    public $timestamps = true;

    protected $fillable = [
        'venue_id',
        'sport_id',
        'nama_lapangan',
        'harga_perjam',
        'deskripsi',
        'tipe_bangunan',
        'jenis_permukaan',
        'panjang',
        'lebar',
        'kapasitas',
        'aturan_lapangan',
        'meta_harga',
        'status_lapangan',
        'alasan_penolakan',
        'submitted_at',
        'reviewed_by',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'harga_perjam' => 'float',
            'panjang' => 'float',
            'lebar' => 'float',
            'meta_harga' => 'array',
            'submitted_at' => 'datetime',
            'reviewed_at' => 'datetime',
        ];
    }

    public function isDraft(): bool
    {
        return $this->status_lapangan === 'draft';
    }

    public function isPendingReview(): bool
    {
        return $this->status_lapangan === 'pending_review';
    }

    public function isAktif(): bool
    {
        return $this->status_lapangan === 'aktif';
    }

    public function isNonaktif(): bool
    {
        return $this->status_lapangan === 'nonaktif';
    }

    public function isRejected(): bool
    {
        return $this->status_lapangan === 'rejected';
    }

    public function getFormattedHargaAttribute(): string
    {
        return 'Rp '.number_format($this->harga_perjam ?? 0, 0, ',', '.');
    }

    public function getDimensiAttribute(): string
    {
        if ($this->panjang && $this->lebar) {
            $p = rtrim(rtrim((string) $this->panjang, '0'), '.');
            $l = rtrim(rtrim((string) $this->lebar, '0'), '.');

            return "{$p} × {$l} m";
        }

        return '-';
    }

    public function getThumbnailUrlAttribute(): string
    {
        $primary = $this->photos->firstWhere('is_primary', true) ?? $this->photos->first();

        if ($primary && $primary->foto_url) {
            if (str_starts_with($primary->foto_url, 'http')) {
                return $primary->foto_url;
            }

            return asset('storage/'.$primary->foto_url);
        }

        return 'https://images.unsplash.com/photo-1574629810360-7efbbe195018?q=80&w=600&auto=format&fit=crop';
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function vanue()
    {
        return $this->venue();
    }

    public function venue()
    {
        return $this->belongsTo(Venue::class, 'venue_id');
    }

    public function sport()
    {
        return $this->belongsTo(MasterSport::class, 'sport_id');
    }

    public function photos()
    {
        return $this->hasMany(FieldPhoto::class, 'field_id');
    }

    public function primaryPhoto()
    {
        return $this->hasOne(FieldPhoto::class, 'field_id')->where('is_primary', true);
    }

    public function schedules()
    {
        return $this->hasMany(FieldSchedule::class, 'field_id');
    }

    public function blocks()
    {
        return $this->hasMany(FieldBlock::class, 'field_id');
    }

    public function bookingDetails()
    {
        return $this->hasMany(BookingDetail::class, 'field_id');
    }

    // Relasi Many-to-Many ke MasterFacilities melalui field_facilities
    public function facilities()
    {
        return $this->belongsToMany(MasterFacility::class, 'field_facilities', 'field_id', 'facilities_id');
    }
}
