<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'saldo_aktif',
        'saldo_tertahan',
        'updated_at',
    ];

    protected $casts = [
        'saldo_aktif' => 'float',
        'saldo_tertahan' => 'float',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class, 'wallet_id');
    }

    public function getFormattedSaldoAktifAttribute(): string
    {
        return 'Rp '.number_format($this->saldo_aktif ?? 0, 0, ',', '.');
    }

    public function getFormattedSaldoTertahanAttribute(): string
    {
        return 'Rp '.number_format($this->saldo_tertahan ?? 0, 0, ',', '.');
    }
}
