<?php

return [
    'nav' => [
        ['label' => null, 'items' => [
            ['label' => 'Dashboard', 'icon' => 'layout-grid', 'route' => 'admin.dashboard'],
        ]],
        ['label' => 'Perlu ditinjau', 'items' => [
            ['label' => 'Verifikasi Mitra', 'icon' => 'user-check', 'route' => null],
            ['label' => 'Moderasi Listing', 'icon' => 'file-check', 'route' => null],
        ]],
        ['label' => 'Platform', 'items' => [
            ['label' => 'Master Data',          'icon' => 'database', 'route' => null],
            ['label' => 'Monitoring Transaksi', 'icon' => 'receipt',  'route' => null],
            ['label' => 'Pengaturan Komisi',    'icon' => 'percent',  'route' => null],
            ['label' => 'Manajemen Pengguna',   'icon' => 'users',    'route' => null],
        ]],
    ],
];