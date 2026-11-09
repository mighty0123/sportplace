@props(['title', 'description' => null, 'tone' => 'light'])
@php
    $dark = $tone === 'dark';
    $hasActions = isset($actions) && $actions->isNotEmpty();
@endphp

<header class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between md:gap-4">
    <div class="min-w-0">
        <h1 @class(['text-h1', 'text-ink' => ! $dark, 'text-white' => $dark])>{{ $title }}</h1>
        @if ($description)
            <p @class(['mt-1 text-body-sm', 'text-ink-muted' => ! $dark, 'text-primary-200' => $dark])>{{ $description }}</p>
        @endif
    </div>

    {{-- Di ponsel kontainer ini hanya ada bila ada tindakan; menu akun disembunyikan --}}
    <div @class(['flex flex-wrap items-center gap-3 md:shrink-0 md:flex-nowrap md:gap-4', 'hidden md:flex' => ! $hasActions])>
        {{ $actions ?? '' }}
        <x-admin.account-menu class="hidden md:block" :dark="$dark" />
    </div>
</header>