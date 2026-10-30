@props(['title', 'description' => null, 'tone' => 'light'])
@php $dark = $tone === 'dark'; @endphp

<header class="flex items-start justify-between gap-4">
    <div class="min-w-0">
        <h1 @class(['text-h1', 'text-ink' => ! $dark, 'text-white' => $dark])>{{ $title }}</h1>
        @if ($description)
            <p @class(['mt-1 text-body-sm', 'text-ink-muted' => ! $dark, 'text-primary-200' => $dark])>{{ $description }}</p>
        @endif
    </div>
    <div class="flex shrink-0 items-center gap-4">
        {{ $actions ?? '' }}
        <x-admin.account-menu class="hidden md:block" :dark="$dark" />
    </div>
</header>