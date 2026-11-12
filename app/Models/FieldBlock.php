<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FieldBlock extends Model
{
    protected $table = 'field_blocks';

    protected $fillable = [
        'field_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'jam_mulai',
        'jam_selesai',
        'alasan_pemblokiran',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class, 'field_id');
    }
}
