<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingDetail extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'header_id', 'field_id', 'tanggal_sewa', 'jam_mulai', 'jam_selesai', 'harga_slot', 'status_item', 'created_at'
    ];

    public function header()
    {
        return $this->belongsTo(BookingHeader::class, 'header_id');
    }

    public function field()
    {
        return $this->belongsTo(Field::class, 'field_id');
    }
}