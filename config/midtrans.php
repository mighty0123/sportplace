<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Midtrans Configuration
    |--------------------------------------------------------------------------
    |
    | File konfigurasi untuk integrasi Midtrans Payment Gateway (Snap).
    | Kunci server dan klien diambil dari file .env sesuai lingkungan (Sandbox / Production).
    |
    */

    'server_key'    => env('MIDTRANS_SERVER_KEY', ''),
    'client_key'    => env('MIDTRANS_CLIENT_KEY', ''),

    // Set false untuk Sandbox/Development simulasi, true untuk Production
    'is_production' => (bool) env('MIDTRANS_IS_PRODUCTION', false),

    // Sanitasi data otomatis sebelum dikirim ke API
    'is_sanitized'  => (bool) env('MIDTRANS_IS_SANITIZED', true),

    // Mengaktifkan 3D Secure untuk verifikasi OTP transaksi kartu kredit
    'is_3ds'        => (bool) env('MIDTRANS_IS_3DS', true),

    // URL library Javascript Snapbox Midtrans untuk frontend
    'snap_url'      => env('MIDTRANS_IS_PRODUCTION', false)
        ? 'https://app.midtrans.com/snap/snap.js'
        : 'https://app.sandbox.midtrans.com/snap/snap.js',

    // URL Endpoint API Snap Backend
    'snap_api_url'  => env('MIDTRANS_IS_PRODUCTION', false)
        ? 'https://app.midtrans.com/snap/v1/transactions'
        : 'https://app.sandbox.midtrans.com/snap/v1/transactions',

    // URL Endpoint API Midtrans Core
    'api_url'       => env('MIDTRANS_IS_PRODUCTION', false)
        ? 'https://api.midtrans.com'
        : 'https://api.sandbox.midtrans.com',

];

