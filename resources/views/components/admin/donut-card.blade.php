@props(['title', 'subtitle' => null, 'unit' => 'data', 'segments', 'empty' => 'Belum ada data.'])
@php
    //[warna garis, warna titik legenda]
    $tones = [
        'success' => ['stroke-success-500', 'bg-success-500'],
        'warning' => ['stroke-warning-500', 'bg-warning-500'],
        'danger'  => ['stroke-danger-500',  'bg-danger-500'],
        'c1'      => ['stroke-chart-1',     'bg-chart-1'],
        'c3'      => ['stroke-chart-3',     'bg-chart-3'],
        'c4'      => ['stroke-chart-4',     'bg-chart-4'],
        'c5'      => ['stroke-chart-5',     'bg-chart-5'],
        'c6'      => ['stroke-chart-6',     'bg-chart-6'],
    ];
    $cycle = ['c1', 'c3', 'c4', 'c5', 'c6'];

    $segs  = collect($segments)->values()->map(fn ($s, $i) => $s + ['tone' => $cycle[$i % count($cycle)]]);
    $total = $segs->sum('value');

    // Busur donat
    $c      = 2 * M_PI * 40;
    $gap    = $segs->where('value', '>', 0)->count() > 1 ? 2.0 : 0.0;
    $offset = 0.0;
    $arcs   = [];
    foreach ($segs as $s) {
        if ($total > 0 && $s['value'] > 0) {
            $len    = $s['value'] / $total * $c;
            $arcs[] = ['len' => max(0, $len - $gap), 'offset' => $offset, 'tone' => $s['tone']];
            $offset += $len;
        }
    }
    $summary = $title . ': ' . $segs->map(fn ($s) => "{$s['label']} {$s['value']}")->implode(', ');
@endphp

<x-ui.card class="flex min-w-0 flex-col lg:min-h-60">
    <h2 class="text-h6 text-ink">{{ $title }}</h2>
    @if ($subtitle)
        <p class="text-caption text-ink-muted">{{ $subtitle }}</p>
    @endif

    <div class="mt-4 flex flex-1 items-center gap-4">
        <svg viewBox="0 0 100 100" class="size-28 shrink-0" role="img" aria-label="{{ $summary }}">
            <circle cx="50" cy="50" r="40" fill="none" stroke-width="14" class="stroke-line"/>
            @foreach ($arcs as $a)
                <circle cx="50" cy="50" r="40" fill="none" stroke-width="14" class="{{ $tones[$a['tone']][0] }}"
                        stroke-dasharray="{{ round($a['len'], 2) }} {{ round($c - $a['len'], 2) }}"
                        stroke-dashoffset="{{ round(-$a['offset'], 2) }}" transform="rotate(-90 50 50)"/>
            @endforeach
            <text x="50" y="49" text-anchor="middle" dominant-baseline="central" font-size="20" font-weight="600" class="fill-ink">{{ $total }}</text>
            <text x="50" y="64" text-anchor="middle" dominant-baseline="central" font-size="7" class="fill-ink-muted">{{ $unit }}</text>
        </svg>

        @if ($total === 0)
            <p class="text-body-sm text-ink-muted">{{ $empty }}</p>
        @else
            <ul class="min-w-0 flex-1 space-y-1.5 text-caption">
                @foreach ($segs as $s)
                    <li class="flex items-center gap-2">
                        <span class="size-2.5 shrink-0 rounded-full {{ $tones[$s['tone']][1] }}" aria-hidden="true"></span>
                        <span class="min-w-0 flex-1 truncate text-ink-body">{{ $s['label'] }}</span>
                        <span class="tabular-nums text-ink">{{ $s['value'] }}</span>
                        <span class="w-9 text-right tabular-nums text-ink-muted">{{ round($s['value'] / $total * 100) }}%</span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</x-ui.card>