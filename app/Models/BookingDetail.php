<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BookingDetail extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'header_id',
        'field_id',
        'tanggal_sewa',
        'jam_mulai',
        'jam_selesai',
        'harga_slot',
        'status_item',
        'created_at',
    ];

    protected $casts = [
        'tanggal_sewa' => 'date',
        'created_at' => 'datetime',
        'harga_slot' => 'double',
    ];

    public function header()
    {
        return $this->belongsTo(BookingHeader::class, 'header_id');
    }

    public function field()
    {
        return $this->belongsTo(Field::class, 'field_id');
    }

    /**
     * Scope query untuk pesanan pada venue milik mitra tertentu (BR-023 / FR-ORDER-001).
     */
    public function scopeForMitra(Builder $query, int $mitraProfileId): Builder
    {
        return $query->whereHas('field.venue', function ($q) use ($mitraProfileId) {
            $q->where('mitra_id', $mitraProfileId);
        });
    }

    /**
     * Scope query untuk pesanan yang siap diselesaikan (jam berakhir sudah lewat).
     */
    public function scopeReadyToComplete(Builder $query): Builder
    {
        $today = Carbon::today()->toDateString();
        $currentTime = Carbon::now()->format('H:i:s');

        return $query->where('status_item', 'ongoing')
            ->whereHas('header', function ($q) {
                $q->where('status', 'payment')
                    ->orWhereHas('payment', fn ($p) => $p->where('status_pembayaran', 'succes'));
            })
            ->where(function ($q) use ($today, $currentTime) {
                $q->where('tanggal_sewa', '<', $today)
                    ->orWhere(function ($sub) use ($today, $currentTime) {
                        $sub->where('tanggal_sewa', '=', $today)
                            ->where('jam_selesai', '<=', $currentTime);
                    });
            });
    }

    /**
     * Periksa apakah tagihan booking sudah dibayar lunas.
     */
    public function isPaid(): bool
    {
        return $this->header?->status === 'payment'
            || $this->header?->payment?->status_pembayaran === 'succes';
    }

    /**
     * Waktu mulai sesi bermain dalam objek Carbon.
     */
    public function getStartDateTime(): Carbon
    {
        $date = $this->tanggal_sewa instanceof Carbon
            ? $this->tanggal_sewa->format('Y-m-d')
            : (string) $this->tanggal_sewa;

        return Carbon::parse($date.' '.$this->jam_mulai);
    }

    /**
     * Waktu berakhir sesi bermain dalam objek Carbon.
     */
    public function getEndDateTime(): Carbon
    {
        $date = $this->tanggal_sewa instanceof Carbon
            ? $this->tanggal_sewa->format('Y-m-d')
            : (string) $this->tanggal_sewa;

        return Carbon::parse($date.' '.$this->jam_selesai);
    }

    /**
     * Periksa apakah sesi bermain dapat ditandai selesai (FR-ORDER-003, AC: ORDER-003-02).
     * Syarat: terbayar, status ongoing, dan waktu selesai sudah terlewati.
     */
    public function isReadyToComplete(): bool
    {
        if ($this->status_item !== 'ongoing') {
            return false;
        }

        if (! $this->isPaid()) {
            return false;
        }

        return Carbon::now()->gte($this->getEndDateTime());
    }

    /**
     * Periksa apakah sesi bermain dapat ditandai no-show (FR-ORDER-002, AC: ORDER-002-01).
     * Syarat: terbayar, status ongoing, jadwal hari ini, dan waktu mulai sudah terlewati.
     */
    public function canMarkNoShow(): bool
    {
        if ($this->status_item !== 'ongoing') {
            return false;
        }

        if (! $this->isPaid()) {
            return false;
        }

        $today = Carbon::today()->toDateString();
        $slotDate = $this->tanggal_sewa instanceof Carbon
            ? $this->tanggal_sewa->format('Y-m-d')
            : (string) $this->tanggal_sewa;

        if ($slotDate !== $today && Carbon::parse($slotDate)->gt(Carbon::today())) {
            return false;
        }

        return Carbon::now()->gte($this->getStartDateTime());
    }

    /**
     * Format harga slot dengan Rupiah.
     */
    public function getFormattedHargaAttribute(): string
    {
        return 'Rp '.number_format($this->harga_slot ?? 0, 0, ',', '.');
    }

    /**
     * Masking nomor telepon untuk proteksi privasi penyewa (NFR-PRIV-001).
     */
    public function getMaskedPhoneAttribute(): string
    {
        $phone = $this->header?->user?->no_hp ?? '';
        if (strlen($phone) <= 6) {
            return $phone !== '' ? '***' : '-';
        }

        return substr($phone, 0, 4).'****'.substr($phone, -3);
    }

    /**
     * Masking alamat email penyewa (NFR-PRIV-001).
     */
    public function getMaskedEmailAttribute(): string
    {
        $email = $this->header?->user?->email ?? '';
        if (! str_contains($email, '@')) {
            return '-';
        }

        [$user, $domain] = explode('@', $email, 2);
        $len = strlen($user);
        $maskedUser = $len > 2
            ? substr($user, 0, 2).str_repeat('*', max(1, $len - 2))
            : $user.'***';

        return $maskedUser.'@'.$domain;
    }
}
