@props(['status', 'label' => null])
@php
    // Satu-satunya tempat yang tahu arti status. [label, nada, ikon]
    $map = [
        'pending'        => ['Menunggu',    'warning', 'clock'],
        'approved'       => ['Disetujui',   'success', 'check-circle'],
        'rejected'       => ['Ditolak',     'danger',  'x-circle'],
        // listing
        'pending_review' => ['Menunggu',    'warning', 'clock'],
        'aktif'          => ['Aktif',       'success', 'check-circle'],
        'nonaktif'       => ['Nonaktif',    'neutral', null],
        'draft'          => ['Draft',       'neutral', null],
        // booking, pembayaran, refund
        'payment'        => ['Dibayar',     'success', 'check-circle'],
        'canceled'       => ['Dibatalkan',  'neutral', 'x-circle'],
        'expired'        => ['Kedaluwarsa', 'neutral', 'clock'],
        'succes'         => ['Berhasil',    'success', 'check-circle'],
        'failed'         => ['Gagal',       'danger',  'x-circle'],
        'processing'     => ['Diproses',    'info',    'clock'],
        'completed'      => ['Selesai',     'success', 'check-circle'],
        'ongoing'        => ['Berjalan',    'info',    'clock'],
        'no_show'        => ['Tidak hadir', 'warning', 'alert-circle'],
        // akun pengguna
        'suspend' => ['Ditangguhkan', 'warning', 'ban'],
        'banned'  => ['Diblokir',     'danger',  'ban'],
    ];
    [$text, $tone, $icon] = $map[$status] ?? [ucfirst((string) $status), 'neutral', null];
@endphp

<x-ui.badge :tone="$tone" :icon="$icon" {{ $attributes }}>{{ $label ?? $text }}</x-ui.badge>