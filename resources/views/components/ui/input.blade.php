@props(['name', 'label', 'hint' => null, 'required' => false, 'type' => 'text'])
@php
    $id = $attributes->get('id', str_replace('.', '-', $name));
    $hasError = $errors->has($name);
    $describedBy = trim(($hint ? "{$id}-hint " : '') . ($hasError ? "{$id}-error" : ''));
@endphp

<div {{ $attributes->only('class') }}>
    <label for="{{ $id }}" class="text-label-md text-ink-strong">
        {{ $label }}@if ($required)<span class="text-danger-700" aria-hidden="true"> *</span>@endif
    </label>

    @if ($hint)
        <p id="{{ $id }}-hint" class="mt-0.5 text-caption text-ink-muted">{{ $hint }}</p>
    @endif

    <input id="{{ $id }}" type="{{ $type }}" name="{{ $name }}" autocomplete="off"
        @if ($required) required @endif
        @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
        @if ($hasError) aria-invalid="true" @endif
        {{ $attributes->except(['class', 'id']) }}
        @class([
            'mt-1.5 min-h-11 w-full rounded-md border bg-surface px-3 text-body-md text-ink placeholder:text-ink-muted lg:min-h-10',
            'focus-visible:outline-2 focus-visible:outline-offset-1',
            'border-line-strong focus-visible:outline-brand' => ! $hasError,
            'border-danger-600 focus-visible:outline-danger-600' => $hasError,
        ])>

    @if ($hasError)
        <p id="{{ $id }}-error" class="mt-1 text-caption text-danger-700">{{ $errors->first($name) }}</p>
    @endif
</div>