<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    public $timestamps = false;
    protected $table = 'venues';

    protected $fillable = [
        'mitra_id', 'city_id', 'nama_venue', 'alamat_lengkap', 
        'map_url', 'jam_buka', 'jam_tutup', 'status_vanue', 'alasan_penolakan'
    ];

    public function mitra()
    {
        return $this->belongsTo(MitraProfile::class, 'mitra_id');
    }

    public function city()
    {
        return $this->belongsTo(MasterCity::class, 'city_id');
    }

    public function fields()
    {
        return $this->hasMany(Field::class, 'venue_id');
    }
}