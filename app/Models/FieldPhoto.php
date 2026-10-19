<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FieldPhoto extends Model
{
    public $timestamps = false;
    protected $table = 'field_photo'; // Di SQL kamu namanya tunggal (photo), bukan photos

    protected $fillable = [
        'field_id', 'foto_url', 'is_primary'
    ];

    public function field()
    {
        return $this->belongsTo(Field::class, 'field_id');
    }
}