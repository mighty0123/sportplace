@props(['name' => null, 'src' => null, 'role' => 'Admin Platform', 'dark' => false])
@php
    $user = auth()->user();
    $useCurrent = $name === null;
    $name = $name ?? $user?->name ?? 'Admin';
    $src = $src ?? ($useCurrent ? $user?->avatar_url : null);
@endphp

<div {{ $attributes->class('flex items-center gap-2.5') }}>
    <x-ui.avatar :name="$name" :src="$src" />
    <div class="leading-tight">
        <p @class(['text-label-md', 'text-ink' => ! $dark, 'text-white' => $dark])>{{ $name }}</p>
        <p @class(['text-caption', 'text-ink-muted' => ! $dark, 'text-primary-200' => $dark])>{{ $role }}</p>
    </div>
</div>