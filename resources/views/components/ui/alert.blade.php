@props(['type' => 'info', 'standalone' => true])
@php
    $styles = [
        'success' => ['border-success-300 bg-success-50 text-success-900', 'check-circle', 'status'],
        'error'   => ['border-danger-300 bg-danger-50 text-danger-900', 'alert-circle', 'alert'],
        'info'    => ['border-info-300 bg-info-50 text-info-900', 'info', 'status'],
    ];
    [$classes, $icon, $role] = $styles[$type] ?? $styles['info'];
@endphp

<div @if ($standalone) x-data="{ show: true }" x-show="show" @endif
    role="{{ $role }}"
    {{ $attributes->class(['flex items-start gap-3 rounded-md border px-4 py-3', $classes]) }}>

    <x-ui.icon :name="$icon" class="mt-0.75" />

    <p class="min-w-0 flex-1 text-body-md">{{ $slot }}</p>

    <button type="button" @click="show = false" aria-label="Tutup pesan"
            class="-my-1 grid size-8 shrink-0 place-items-center rounded-md hover:bg-black/5 focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-current">
        <x-ui.icon name="x" />
    </button>
</div>