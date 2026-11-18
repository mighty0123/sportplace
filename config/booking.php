<?php

return [
    // lama booking menunggu pembayaran sebelum kedaluwarsa
    'payment_window_minutes' => 15,

    // pembatalan hanya boleh bila jam mulai slot masih lebih dari sekian jam lagi
    'cancellation_min_hours' => 24,

    // persentase refund untuk pembatalan yang memenuhi syarat
    'refund_percent' => 100,

    'poll_reconcile' => (bool) env('BOOKING_POLL_RECONCILE', true),
    'reconcile_interval_seconds' => 10,
    
    // Biaya layanan platform per booking (rupiah)
    'service_fee' => (int) env('BOOKING_SERVICE_FEE', 2500),

    // Satuan terkecil reservasi slot (menit).
    'slot_minutes' => (int) env('BOOKING_SLOT_MINUTES', 30),
];