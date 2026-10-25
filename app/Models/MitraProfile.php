<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MitraProfile extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'nama_usaha',
        'bentuk_badan_usaha',
        'kota_operasional',
        'deskripsi_usaha',
        'bank_nama',
        'nomor_rekening',
        'nama_rekening',
        'no_ktp',
        'npwp',
        'status',
        'alasan_penolakan',
        'file_ktp',
        'file_npwp',
        'file_nib',
        'file_sewa_lahan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function venues()
    {
        return $this->hasMany(Venue::class, 'mitra_id');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
