@props(['checks'])
@php
    $all    = collect($checks);
    $issues = $all->whereIn('status', ['fail', 'warn'])->sortBy(fn ($c) => $c['status'] === 'fail' ? 0 : 1)->values();
    $rest   = $all->whereNotIn('status', ['fail', 'warn'])->values();
    $nFail  = $all->where('status', 'fail')->count();
    $nWarn  = $all->where('status', 'warn')->count();
    $nPass  = $rest->where('status', 'pass')->count();
    $nNa    = $rest->where('status', 'na')->count();

    $summary = $issues->isEmpty()
        ? 'Semua pemeriksaan lolos.'
        : collect([[$nFail, 'tidak lolos'], [$nWarn, 'perlu perhatian']])->filter(fn ($p) => $p[0] > 0)
            ->map(fn ($p) => "{$p[0]} {$p[1]}")->implode(', ') . '.';

    // [ikon, warna ikon, kata status, warna kata status, kotak]
    $styles = [
        'fail' => ['x-circle',     'text-danger-700',  'Tidak lolos',     'text-danger-700',  'border-danger-500 bg-danger-50'],
        'warn' => ['alert-circle', 'text-warning-700', 'Perlu perhatian', 'text-warning-800', 'border-warning-500 bg-warning-50'],
        'pass' => ['check-circle', 'text-success-700', 'Lolos',           'text-success-800', ''],
        'na'   => ['info',         'text-ink-muted',   'Tidak berlaku',   'text-ink-muted',   ''],
    ];
@endphp

<section aria-labelledby="data-checks-title">
    <h2 id="data-checks-title" class="text-h6 text-ink">Pemeriksaan data</h2>
    <p class="text-caption text-ink-muted">{{ $summary }}</p>

    {{-- masalah lebih dulu --}}
    @if ($issues->isNotEmpty())
        <ul class="mt-3 space-y-2">
            @foreach ($issues as $c)
                @php [$icon, $iconColor, $word, $wordColor, $box] = $styles[$c['status']]; @endphp
                <li class="flex gap-3 rounded-md border-l-4 px-3 py-2.5 {{ $box }}">
                    <x-ui.icon :name="$icon" class="mt-0.5 {{ $iconColor }}" />
                    <div class="min-w-0">
                        <p class="text-body-sm text-ink">
                            <span class="font-semibold">{{ $c['label'] }}</span>
                            <span class="ml-1 text-label-sm {{ $wordColor }}">{{ $word }}</span>
                        </p>
                        <p class="mt-0.5 text-body-sm text-ink-body">{{ $c['detail'] }}</p>
                        @foreach ($c['links'] as $link)
                            <a href="{{ $link['url'] }}" class="block text-caption text-brand-link underline">{{ $link['label'] }}</a>
                        @endforeach
                    </div>
                </li>
            @endforeach
        </ul>
    @endif

    {{-- terbuka hanya bila tidak ada masalah. --}}
    @if ($rest->isNotEmpty())
        <details class="group mt-3" @if ($issues->isEmpty()) open @endif>
            <summary class="flex min-h-9 cursor-pointer list-none items-center justify-between gap-2 rounded-md text-label-md text-ink-body hover:text-ink [&::-webkit-details-marker]:hidden">
                <span>
                    {{ $nPass }} pemeriksaan lolos{{ $nNa ? ", {$nNa} tidak berlaku" : '' }}
                </span>
                <x-ui.icon name="chevron-down" class="transition-transform group-open:rotate-180" />
            </summary>

            <ul class="mt-1 divide-y divide-line">
                @foreach ($rest as $c)
                    @php [$icon, $iconColor, $word] = $styles[$c['status']]; @endphp
                    <li class="flex gap-3 py-2.5">
                        <x-ui.icon :name="$icon" class="mt-0.5 {{ $iconColor }}" />
                        <div class="min-w-0">
                            <p class="text-body-sm text-ink">{{ $c['label'] }}<span class="sr-only">: {{ $word }}</span></p>
                            <p class="text-caption text-ink-muted">{{ $c['detail'] }}</p>
                        </div>
                    </li>
                @endforeach
            </ul>
        </details>
    @endif

    <p class="mt-3 text-caption text-ink-muted">
        Dihitung otomatis dari data yang diajukan. Hasil ini membantu, tetapi keputusan tetap di tangan admin.
    </p>
</section>