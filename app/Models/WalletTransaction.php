<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'wallet_id',
        'jenis_mutasi',
        'kategori_transaksi',
        'nominal',
        'referensi_tipe',
        'referensi_id',
        'deskripsi',
        'created_at',
    ];

    protected $casts = [
        'nominal' => 'float',
        'created_at' => 'datetime',
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class, 'wallet_id');
    }

    public function getFormattedNominalAttribute(): string
    {
        $prefix = $this->jenis_mutasi === 'kredit' ? '+' : '-';

        return $prefix.' Rp '.number_format($this->nominal ?? 0, 0, ',', '.');
    }
}
