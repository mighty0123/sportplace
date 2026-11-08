<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FieldSchedule extends Model
{
    public $timestamps = false;

    protected $table = 'field_schedule';

    protected $fillable = [
        'field_id', 'hari', 'jam_buka', 'jam_tutup', 'durasi', 'status_hari',
    ];

    public function field()
    {
        return $this->belongsTo(Field::class, 'field_id');
    }
}
