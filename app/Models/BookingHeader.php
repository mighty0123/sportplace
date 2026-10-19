<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingHeader extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'penyewa_id', 'total_tagihan', 'status', 'created_at'
    ];

    public function user()
    {
        // Relasi ke tabel users dengan foreign key 'penyewa_id'
        return $this->belongsTo(User::class, 'penyewa_id');
    }

    public function details()
    {
        return $this->hasMany(BookingDetail::class, 'header_id');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'header_id');
    }
}