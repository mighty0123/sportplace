<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MitraProfile extends Model
{
    public $timestamps = false; // Karena di tabel tidak ada created_at/updated_at
    
    protected $fillable = [
        'user_id', 'no_ktp', 'npwp', 'status', 'alasan_penolakan'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function venues()
    {
        return $this->hasMany(Venue::class, 'mitra_id');
    }
}