<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Tarif Komisi Platform (GAP-07 / OQ-004 / BR-017)
    |--------------------------------------------------------------------------
    | Persentase potongan komisi platform dari pendapatan sewa lapangan mitra.
    | Nilai default adalah 0.0 (ditampilkan jelas sebagai belum ditetapkan
    | oleh manajemen produk jika bernilai 0).
    |
    | Dapat dioverride melalui environment PLATFORM_COMMISSION_PERCENT (misal 5.0 atau 10.0).
    */
    'rate_percent' => (float) env('PLATFORM_COMMISSION_PERCENT', 0.0),

    /*
    | Status konfigurasi komisi
    */
    'is_configured' => env('PLATFORM_COMMISSION_PERCENT') !== null,
];
