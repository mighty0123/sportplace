<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'bank_kode', 'nomor_rekening', 'nama_pemilik_rek', 'nominal_tarik', 'biaya_admin_bank', 'status', 'pg_disbursement_id', 'alasan_gagal', 'created_at', 'complated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}