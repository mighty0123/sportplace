<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FieldSchedule extends Model
{
    public const DAYS = [
        1 => 'Senin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
        6 => 'Sabtu',
        7 => 'Minggu',
    ];

    protected $table = 'field_schedule';

    protected $fillable = [
        'field_id',
        'hari',
        'jam_buka',
        'jam_tutup',
        'durasi',
        'status_hari',
    ];

    protected $casts = [
        'hari' => 'integer',
    ];

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class, 'field_id');
    }

    public function getNamaHariAttribute(): string
    {
        return self::DAYS[$this->hari] ?? "Hari {$this->hari}";
    }
}
