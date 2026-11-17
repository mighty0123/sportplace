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
];