@props(['status'])
@php
    $map = [
        'pending'        => ['Menunggu',  'warning', 'clock'],
        'approved'       => ['Disetujui', 'success', 'check-circle'],
        'rejected'       => ['Ditolak',   'danger',  'x-circle'],
        // status listing (venue dan lapangan)
        'pending_review' => ['Menunggu',  'warning', 'clock'],
        'aktif'          => ['Aktif',     'success', 'check-circle'],
        'nonaktif'       => ['Nonaktif',  'neutral', null],
        'draft'          => ['Draft',     'neutral', null],
    ];
    [$label, $tone, $icon] = $map[$status] ?? [ucfirst((string) $status), 'neutral', null];
@endphp

<x-ui.badge :tone="$tone" :icon="$icon" {{ $attributes }}>{{ $label }}</x-ui.badge>