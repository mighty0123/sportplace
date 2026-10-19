<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterFacility extends Model
{
    public $timestamps = false;
    protected $table = 'master_facilities'; // Agar tidak salah mendeteksi nama tabel plural

    protected $fillable = [
        'nama', 'kategori', 'icon_url', 'status_fasilitas'
    ];

    public function fields()
    {
        return $this->belongsToMany(Field::class, 'field_facilities', 'facilities_id', 'field_id');
    }
}