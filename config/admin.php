<?php

return [
    'verification_sla_days' => 2,
    'listing_photo_disk' => 'public',
    'master_icon_disk' => 'public',

    'nav' => [
        [
            'label' => null,
            'items' => [
                [
                    'label' => 'Dashboard',
                    'icon' => 'layout-grid',
                    'route' => 'admin.dashboard',
                ],
            ],
        ],

        [
            'label' => 'Perlu ditinjau',
            'items' => [
                [
                    'key' => 'verifikasi',
                    'label' => 'Verifikasi Mitra',
                    'icon' => 'user-check',
                    'route' => 'admin.verifikasi.index',
                    'active' => 'admin.verifikasi.*',
                ],
                [
                    'key' => 'moderasi', 
                    'label' => 'Moderasi Listing',
                    'icon' => 'file-check',
                    'route' => 'admin.moderasi.index',
                    'active' => 'admin.moderasi.*'
                ],
            ],
        ],

        [
            'label' => 'Platform',
            'items' => [
                [
                    'label' => 'Master Data',
                    'icon' => 'database',
                    'route' => 'admin.master.index',
                    'active' => 'admin.master.*'
                ],
                [
                    'label' => 'Monitoring Transaksi',
                    'icon' => 'receipt',
                    'route' => 'admin.transaksi.index',
                    'active' => 'admin.transaksi.*'
                ],
                [
                    'label' => 'Laporan',
                    'icon' => 'file-text', 
                    'route' => 'admin.laporan.index', 
                    'active' => 'admin.laporan.*'],
                [
                    'label' => 'Pengaturan Komisi',
                    'icon' => 'percent',
                    'route' => null,
                    'disabled' => true,
                ],
                [
                    'label' => 'Manajemen Pengguna',
                    'icon' => 'users',
                    'route' => null,
                ],
            ],
        ],
    ],
];
