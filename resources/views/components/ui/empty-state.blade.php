@props(['title', 'description' => null, 'icon' => 'inbox'])

<div {{ $attributes->class('flex flex-col items-center px-4 py-10 text-center') }}>
    <span class="grid size-12 place-items-center rounded-full bg-surface-sunken text-ink-muted">
        <x-ui.icon :name="$icon" class="size-6" />
    </span>
    <h3 class="mt-3 text-h5 text-ink">{{ $title }}</h3>
    @if ($description)
        <p class="mt-1 max-w-sm text-body-sm text-ink-muted">{{ $description }}</p>
    @endif
    @if (! $slot->isEmpty())
        <div class="mt-4">{{ $slot }}</div>
    @endif
</div>