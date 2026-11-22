<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FieldPhoto extends Model
{
    public $timestamps = false;

    protected $table = 'field_photo'; // Di SQL kamu namanya tunggal (photo), bukan photos

    protected $fillable = [
        'field_id', 'foto_url', 'is_primary',
    ];

    public function field()
    {
        return $this->belongsTo(Field::class, 'field_id');
    }

    /**
     * Mengembalikan URL absolut yang valid untuk foto lapangan.
     */
    public function getUrlAttribute(): string
    {
        if (empty($this->foto_url)) {
            return asset('images/courts/futsal.jpg');
        }

        if (str_starts_with($this->foto_url, 'http://') || str_starts_with($this->foto_url, 'https://')) {
            return $this->foto_url;
        }

        if (str_starts_with($this->foto_url, 'images/')) {
            return asset($this->foto_url);
        }

        if (str_starts_with($this->foto_url, 'storage/')) {
            return asset($this->foto_url);
        }

        return asset('storage/' . $this->foto_url);
    }
}
