@props(['status'])
@php
    $map = [
        'pending'  => ['Menunggu',  'warning', 'clock'],
        'approved' => ['Disetujui', 'success', 'check-circle'],
        'rejected' => ['Ditolak',   'danger',  'x-circle'],
    ];
    [$label, $tone, $icon] = $map[$status] ?? [ucfirst((string) $status), 'neutral', null];
@endphp

<x-ui.badge :tone="$tone" :icon="$icon" {{ $attributes }}>{{ $label }}</x-ui.badge>