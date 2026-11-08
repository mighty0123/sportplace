<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'wallet_id', 'jenis_mutasi', 'kategori_transaksi', 'nominal', 'referensi_tipe', 'referensi_id', 'deskripsi', 'created_at',
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class, 'wallet_id');
    }
}
