@props(['title', 'description' => null, 'icon' => null, 'variant' => null])
@php
    $variant ??= ($icon === 'search' ? 'search' : 'empty');

    $court = '<circle cx="80" cy="56" r="52" fill="#ddf4ef"/>'
        . '<rect x="30" y="24" width="100" height="64" rx="6" fill="#fcfbf5" fill-opacity=".7" stroke="#0a7660" stroke-width="2.5"/>'
        . '<path d="M80 24V88" stroke="#0a7660" stroke-width="2.5" opacity=".55"/>'
        . '<circle cx="80" cy="56" r="14" fill="none" stroke="#0a7660" stroke-width="2.5" opacity=".55"/>'
        . '<path d="M30 40H46V72H30M130 40H114V72H130" fill="none" stroke="#0a7660" stroke-width="2.5" stroke-linejoin="round" opacity=".55"/>';

    $ball = function (float $x, float $y, float $r) {
        $k = $r / 10;
        $s = fn (float $v) => round($v * $k, 2);

        return "<g transform=\"translate({$x} {$y})\"><circle r=\"{$r}\" fill=\"#e2ee58\" stroke=\"#0a7660\" stroke-width=\"2\"/>"
            . '<path d="M' . $s(-8) . ' ' . $s(-4) . 'C' . $s(-2) . ' ' . $s(-1.5) . ' ' . $s(-2) . ' ' . $s(1.5) . ' ' . $s(-8) . ' ' . $s(4)
            . 'M' . $s(8) . ' ' . $s(-4) . 'C' . $s(2) . ' ' . $s(-1.5) . ' ' . $s(2) . ' ' . $s(1.5) . ' ' . $s(8) . ' ' . $s(4)
            . '" fill="none" stroke="#fcfbf5" stroke-width="1.7" stroke-linecap="round"/></g>';
    };
@endphp

<div {{ $attributes->class('flex flex-col items-center px-4 py-10 text-center') }}>
    <svg viewBox="0 0 160 112" fill="none" class="mx-auto h-auto w-32" aria-hidden="true">
        {!! $court !!}
        @switch($variant)
            @case('search')
                <path d="M101 71 116 86" fill="none" stroke="#0b604f" stroke-width="6" stroke-linecap="round"/>
                <circle cx="88" cy="58" r="18" fill="#fcfbf5" stroke="#0b604f" stroke-width="4"/>
                {!! $ball(88, 58, 8) !!}
                @break
            @case('done')
                <ellipse cx="110" cy="80" rx="8" ry="2" fill="#0a7660" opacity=".18"/>
                {!! $ball(110, 73, 7) !!}
                <circle cx="80" cy="54" r="15" fill="#2dbe62"/>
                <path d="m72.5 54.5 5.5 5.5 10-11" fill="none" stroke="#fff" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                @break
            @default
                <ellipse cx="80" cy="68" rx="9" ry="2.4" fill="#0a7660" opacity=".18"/>
                {!! $ball(80, 56, 10) !!}
        @endswitch
    </svg>

    <h3 class="mt-4 text-h5 text-ink">{{ $title }}</h3>
    @if ($description)
        <p class="mt-1 max-w-sm text-body-sm text-ink-muted">{{ $description }}</p>
    @endif
    @if (! $slot->isEmpty())
        <div class="mt-4">{{ $slot }}</div>
    @endif
</div>