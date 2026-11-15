<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'header_id',
        'pg_transaction_id',
        'metode_pembayaran',
        'nominal_bayar',
        'status_pembayaran',
        'paid_at',
        'created_at',
    ];

    protected $casts = [
        'nominal_bayar' => 'double',
        'paid_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function header()
    {
        return $this->belongsTo(BookingHeader::class, 'header_id');
    }

    public function isSucces(): bool
    {
        return $this->status_pembayaran === 'succes';
    }

    public function getFormattedNominalAttribute(): string
    {
        return 'Rp '.number_format($this->nominal_bayar ?? 0, 0, ',', '.');
    }
}
