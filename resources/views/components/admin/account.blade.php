@props(['name' => null, 'role' => 'Admin Platform', 'dark' => false])
@php $name = $name ?? auth()->user()?->name ?? 'Admin'; @endphp

<div {{ $attributes->class('flex items-center gap-2.5') }}>
    <x-ui.avatar :name="$name" />
    <div class="leading-tight">
        <p @class(['text-label-md', 'text-ink' => ! $dark, 'text-white' => $dark])>{{ $name }}</p>
        <p @class(['text-caption', 'text-ink-muted' => ! $dark, 'text-primary-200' => $dark])>{{ $role }}</p>
    </div>
</div>