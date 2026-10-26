<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterCity extends Model
{
    public $timestamps = false;

    protected $table = 'master_cities';

    protected $fillable = [
        'nama_kota', 'provinsi', 'status_kota',
    ];

    public function vanues()
    {
        return $this->venues();
    }

    public function venues()
    {
        return $this->hasMany(Venue::class, 'city_id');
    }
}
