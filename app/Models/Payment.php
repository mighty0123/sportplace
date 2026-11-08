<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'header_id', 'pg_transaction_id', 'metode_pembayaran', 'nominal_bayar', 'status_pembayaran', 'paid_at', 'created_at',
    ];

    public function header()
    {
        return $this->belongsTo(BookingHeader::class, 'header_id');
    }
}
