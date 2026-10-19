<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'header_id', 'detail_id', 'payment_id', 'alasan_refund', 'nominal_refund', 'metode_refund', 'rekening_tujuan', 'status_refund', 'diproses_oleh', 'complated_at'
    ];

    public function header()
    {
        return $this->belongsTo(BookingHeader::class, 'header_id');
    }

    public function detail()
    {
        return $this->belongsTo(BookingDetail::class, 'detail_id');
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    public function adminProcessor()
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }
}