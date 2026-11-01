<?php

return [
    'verification_sla_days' => 2,

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
                    'route' => null,
                ],
            ],
        ],

        [
            'label' => 'Platform',
            'items' => [
                [
                    'label' => 'Master Data',
                    'icon' => 'database',
                    'route' => null,
                ],
                [
                    'label' => 'Monitoring Transaksi',
                    'icon' => 'receipt',
                    'route' => null,
                ],
                [
                    'label' => 'Laporan',
                    'icon' => 'file-text',
                    'route' => null,
                ],
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