@props(['items'])

<dl class="grid grid-cols-2 gap-px overflow-hidden rounded-md border border-line bg-line md:grid-cols-4">
    @foreach ($items as $i)
        @php $alert = $i['alert'] ?? false; @endphp
        <div class="bg-surface p-4">
            <dt class="text-caption text-ink-muted">{{ $i['label'] }}</dt>
            <dd class="mt-1 flex items-center gap-2 text-metric tabular-nums {{ $alert ? 'text-danger-700' : 'text-ink' }}">
                {{ $i['value'] }}
                @if ($alert)<x-ui.icon name="alert-circle" /><span class="sr-only">perlu perhatian</span>@endif
            </dd>
            @if (! empty($i['note']))
                <p class="mt-0.5 truncate text-caption tabular-nums text-ink-muted">{{ $i['note'] }}</p>
            @endif
        </div>
    @endforeach
</dl>