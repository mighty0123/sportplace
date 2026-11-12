<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentWebhookEvent extends Model
{
    public $timestamps = false;

    protected $table = 'payment_webhook_events';

    protected $fillable = [
        'provider',
        'event_id',
        'pg_transaction_id',
        'event_type',
        'payment_id',
        'signature_valid',
        'payload',
        'process_status',
        'error',
        'received_at',
        'processed_at',
    ];

    protected $casts = [
        'signature_valid' => 'boolean',
        'payload'         => 'array',
        'received_at'     => 'datetime',
        'processed_at'    => 'datetime',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }
}
