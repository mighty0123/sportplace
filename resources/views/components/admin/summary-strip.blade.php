@props(['items'])

<dl class="grid grid-cols-2 gap-px overflow-hidden rounded-md border border-line bg-line lg:grid-cols-4">
    @foreach ($items as $i)
        @php
            $alert   = $i['alert'] ?? false;
            $compact = $i['compact'] ?? false;
        @endphp
        <div class="min-w-0 bg-surface p-4">
            <dt class="truncate text-caption text-ink-muted">{{ $i['label'] }}</dt>
            <dd @class([
                'mt-1 flex min-h-10 items-center gap-2 tabular-nums',
                'text-metric' => ! $compact,
                'text-h3' => $compact,
                'text-danger-700' => $alert,
                'text-ink' => ! $alert,
            ])>
                {{ $i['value'] }}
                @if ($alert)<x-ui.icon name="alert-circle" /><span class="sr-only">perlu perhatian</span>@endif
            </dd>
            @if (! empty($i['note']))
                <p class="mt-0.5 truncate text-caption text-ink-muted">{{ $i['note'] }}</p>
            @endif
        </div>
    @endforeach
</dl>