@props(['status', 'waiting' => null, 'reviewedAt' => null, 'reviewer' => null, 'reason' => null, 'sla' => 2])
@php
    $since = $waiting ? \Carbon\CarbonImmutable::parse($waiting) : null;
    $days  = $since ? (int) floor($since->diffInDays(now())) : null;
    $late  = $status === 'pending_review' && $days !== null && $days >= $sla;
    $fmt   = fn ($d) => $d ? \Carbon\CarbonImmutable::parse($d)->locale('id')->translatedFormat('j M Y') : null;
@endphp

<div {{ $attributes }}>
    @if ($status === 'pending_review')
        <p class="text-body-sm text-ink">{{ $fmt($waiting) ?? 'Tanggal tidak tercatat' }}</p>
        @if ($late)
            <x-ui.badge tone="danger" icon="alert-circle" class="mt-1">Menunggu {{ $days }} hari</x-ui.badge>
        @elseif ($days !== null)
            <p class="text-caption text-ink-muted">{{ $days === 0 ? 'Diajukan hari ini' : "Menunggu {$days} hari" }}</p>
        @endif
    @else
        <p class="text-body-sm text-ink">{{ $fmt($reviewedAt) ?? 'Tanggal tidak tercatat' }}</p>
        <p class="text-caption text-ink-muted">oleh {{ $reviewer ?? 'admin (tidak tercatat)' }}</p>
        @if ($status === 'rejected' && $reason)
            <p class="mt-1 text-caption text-ink-body" title="{{ $reason }}">{{ \Illuminate\Support\Str::limit($reason, 60) }}</p>
        @endif
    @endif
</div>