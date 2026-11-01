@props(['checks'])
@php
    // Masalah di atas: yang perlu dilihat admin tidak tenggelam di antara yang lolos.
    $order  = ['fail' => 0, 'warn' => 1, 'pass' => 2, 'na' => 3];
    $sorted = collect($checks)->sortBy(fn ($c) => $order[$c['status']])->values();
    $n      = fn ($status) => collect($checks)->where('status', $status)->count();

    $summary = collect([
        [$n('fail'), 'tidak lolos'], [$n('warn'), 'perlu perhatian'], [$n('pass'), 'lolos'],
    ])->filter(fn ($p) => $p[0] > 0)->map(fn ($p) => "{$p[0]} {$p[1]}")->implode(', ');

    // Ikon, warna, dan kata sekaligus: status tidak bergantung pada warna saja.
    $styles = [
        'pass' => ['check-circle', 'text-success-700', 'lolos'],
        'warn' => ['alert-circle', 'text-warning-700', 'perlu perhatian'],
        'fail' => ['x-circle', 'text-danger-700', 'tidak lolos'],
        'na'   => ['info', 'text-ink-muted', 'tidak berlaku'],
    ];
@endphp

<section aria-labelledby="data-checks-title">
    <h2 id="data-checks-title" class="text-h6 text-ink">Pemeriksaan data</h2>
    <p class="text-caption text-ink-muted">{{ $summary ?: 'Tidak ada pemeriksaan yang berlaku.' }}</p>

    <ul class="mt-2 divide-y divide-line">
        @foreach ($sorted as $c)
            @php [$icon, $color, $word] = $styles[$c['status']]; @endphp
            <li class="flex gap-3 py-2.5">
                <x-ui.icon :name="$icon" class="mt-0.5 {{ $color }}" />
                <div class="min-w-0">
                    <p class="text-body-sm text-ink">{{ $c['label'] }}<span class="sr-only">: {{ $word }}</span></p>
                    <p class="text-caption text-ink-muted">{{ $c['detail'] }}</p>
                    @foreach ($c['links'] as $link)
                        <a href="{{ $link['url'] }}" class="block text-caption text-brand-link underline">{{ $link['label'] }}</a>
                    @endforeach
                </div>
            </li>
        @endforeach
    </ul>

    <p class="mt-2 text-caption text-ink-muted">
        Dihitung otomatis dari data pendaftaran. Hasil ini membantu, tetapi keputusan tetap di tangan admin.
    </p>
</section>