@php
    $cols = collect($d->columns)->where('pdf', true)->values();
    $fmt = fn (string $kind, $v) => match ($kind) {
        'money'    => number_format((float) $v, 0, ',', '.'),
        'int'      => number_format((int) $v, 0, ',', '.'),
        'date'     => $v ? $v->locale('id')->translatedFormat('j M Y') : '-',
        'datetime' => $v ? $v->locale('id')->translatedFormat('j M Y, H.i') : '-',
        'bool'     => $v ? 'Ya' : 'Tidak',
        default    => (string) ($v ?? '-'),
    };
    $num = fn (string $kind) => in_array($kind, ['money', 'int'], true);
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $d->title }}</title>
    <style>
        @page { margin: 36px 36px 48px 36px; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 8.5pt; color: #1b2724; }
        h1 { font-size: 16pt; margin: 0; color: #0b604f; }
        h2 { font-size: 12pt; margin: 2px 0 8px; }
        .muted { color: #5a6c5b; }
        .kop { border-bottom: 2px solid #0a7660; padding-bottom: 8px; margin-bottom: 10px; }
        .meta { margin: 0 0 10px; line-height: 1.5; }
        table { width: 100%; border-collapse: collapse; }
        .sum td { padding: 3px 8px 3px 0; }
        .sum .v { text-align: right; font-weight: bold; padding-right: 28px; }
        thead { display: table-header-group; }
        th { background: #0a7660; color: #fff; text-align: left; padding: 5px 6px; font-size: 8pt; }
        td { padding: 4px 6px; border-bottom: 1px solid #e1e3d9; vertical-align: top; }
        tr { page-break-inside: avoid; }
        tbody tr:nth-child(even) td { background: #f8f7f0; }
        .r { text-align: right; }
    </style>
</head>
<body>
    <div class="kop">
        <h1>SportPlace</h1>
        <h2>{{ $d->title }}</h2>
        <p class="meta muted">
            @foreach ($d->filterLines as $line){{ $line }}<br>@endforeach
            Dicetak oleh {{ $admin }}, {{ $printedAt }}
        </p>
    </div>

    @if ($d->summary)
        <table class="sum" style="width: auto; margin-bottom: 12px;">
            @foreach (array_chunk($d->summary, 4) as $chunk)
                <tr>
                    @foreach ($chunk as [$label, $value, $kind])
                        <td class="muted">{{ $label }}</td>
                        <td class="v">{{ $fmt($kind, $value) }}</td>
                    @endforeach
                </tr>
            @endforeach
        </table>
    @endif

    <table>
        <thead>
            <tr>
                @foreach ($cols as $c)
                    <th class="{{ $num($c['kind']) ? 'r' : '' }}">{{ $c['label'] }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($d->rows as $row)
                <tr>
                    @foreach ($cols as $c)
                        <td class="{{ $num($c['kind']) ? 'r' : '' }}">{{ $fmt($c['kind'], $row[$c['key']] ?? null) }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>