@props(['mitra', 'sla' => 2])
@php
    $at   = $mitra->submitted_at;
    $days = $mitra->daysWaiting();
    $late = $mitra->isPending() && $days !== null && $days >= $sla;
    $fmt  = fn ($d) => $d?->locale('id')->translatedFormat('j M Y');
@endphp

<div {{ $attributes }}>
    @if ($mitra->isPending())
        <p class="text-body-sm text-ink">{{ $fmt($at) ?? 'Tanggal tidak tercatat' }}</p>
        @if ($late)
            {{-- Ikon, teks, dan warna sekaligus --}}
            <x-ui.badge tone="danger" icon="alert-circle" class="mt-1">Menunggu {{ $days }} hari</x-ui.badge>
        @elseif ($days !== null)
            <p class="text-caption text-ink-muted">{{ $days === 0 ? 'Diajukan hari ini' : "Menunggu {$days} hari" }}</p>
        @endif
    @else
        <p class="text-body-sm text-ink">{{ $fmt($mitra->reviewed_at) ?? 'Tanggal tidak tercatat' }}</p>
        <p class="text-caption text-ink-muted">oleh {{ $mitra->reviewer?->name ?? 'admin (tidak tercatat)' }}</p>
        @if ($mitra->isRejected() && $mitra->alasan_penolakan)
            <p class="mt-1 text-caption text-ink-body" title="{{ $mitra->alasan_penolakan }}">{{ \Illuminate\Support\Str::limit($mitra->alasan_penolakan, 60) }}</p>
        @endif
    @endif
</div>