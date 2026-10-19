<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FieldBlock extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'field_id', 'tanggal_mulai', 'tanggal_selesai', 'jam_mulai', 'jam_selesai', 'alasan_pemblokiran'
    ];

    public function field()
    {
        return $this->belongsTo(Field::class, 'field_id');
    }
}