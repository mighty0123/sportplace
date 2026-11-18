<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'bank_kode',
        'nomor_rekening',
        'nama_pemilik_rek',
        'nominal_tarik',
        'biaya_admin_bank',
        'status',
        'pg_disbursement_id',
        'alasan_gagal',
        'created_at',
        'complated_at',
    ];

    protected $casts = [
        'nominal_tarik' => 'float',
        'biaya_admin_bank' => 'float',
        'created_at' => 'datetime',
        'complated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedNominalAttribute(): string
    {
        return 'Rp '.number_format($this->nominal_tarik ?? 0, 0, ',', '.');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Proses',
            'processing' => 'Sedang Diproses',
            'completed' => 'Berhasil',
            'failed' => 'Gagal',
            default => ucfirst((string) $this->status),
        };
    }
}
