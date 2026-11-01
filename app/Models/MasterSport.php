<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterSport extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'nama_olahraga', 'slug', 'icon_url', 'status_olahraga',
    ];

    public function fields()
    {
        return $this->hasMany(Field::class, 'sport_id');
    }
}
