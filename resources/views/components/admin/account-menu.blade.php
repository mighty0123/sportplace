@props(['dark' => false, 'compact' => false])
@php
    $user = auth()->user();
    $profileUrl = \Illuminate\Support\Facades\Route::has('admin.profile') ? route('admin.profile') : null;
    $item = 'flex min-h-11 w-full items-center gap-2.5 rounded-sm px-2.5 text-label-md';
@endphp

<div x-data="{ open: false }"
    @keydown.escape.window="open = false"
    @click.outside="open = false"
    {{ $attributes->class('relative') }}>

    @if ($compact)
        {{-- Mobile: hanya avatar --}}
        <button type="button" @click="open = !open" :aria-expanded="open" aria-haspopup="menu" aria-label="Menu akun"
                class="grid size-11 place-items-center rounded-lg hover:bg-primary-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-secondary-400">
            <x-ui.avatar :name="$user?->name ?? 'Admin'" />
        </button>
    @else
        <button type="button" @click="open = !open" :aria-expanded="open" aria-haspopup="menu" aria-label="Menu akun"
                @class([
                    'flex items-center gap-2 rounded-lg p-1 pr-2 text-left transition-colors focus-visible:outline-2 focus-visible:outline-offset-2',
                    'hover:bg-white/10 focus-visible:outline-secondary-400' => $dark,
                    'hover:bg-surface-muted focus-visible:outline-brand' => ! $dark,
                ])>
            <x-admin.account :dark="$dark" />
            <x-ui.icon name="chevron-down"
                ::class="open && 'rotate-180'"
                @class(['transition-transform motion-reduce:transition-none', 'text-primary-200' => $dark, 'text-ink-muted' => ! $dark]) />
        </button>
    @endif

    <div x-show="open" x-cloak x-transition.opacity.duration.150ms role="menu"
        class="absolute right-0 top-full z-50 mt-2 w-60 rounded-md border border-line bg-surface p-1.5 shadow-float">
        <div class="px-2.5 py-2">
            <p class="truncate text-label-md text-ink">{{ $user?->name }}</p>
            <p class="truncate text-caption text-ink-muted">{{ $user?->email }}</p>
        </div>
        <hr class="my-1 border-line">

        @if ($profileUrl)
            <a href="{{ $profileUrl }}" role="menuitem" class="{{ $item }} text-ink-body hover:bg-surface-muted">
                <x-ui.icon name="user" /> Profil saya
            </a>
        @else
            <span role="menuitem" aria-disabled="true" class="{{ $item }} text-ink-muted">
                <x-ui.icon name="user" /> Profil saya
                <span class="ml-auto text-caption">Segera hadir</span>
            </span>
        @endif

        <button type="button" role="menuitem"
            @click="open = false; $dispatch('open-modal', 'logout')"
            class="{{ $item }} text-danger-700 hover:bg-danger-50">
        <x-ui.icon name="log-out" /> Keluar
    </button>
    </div>
</div>