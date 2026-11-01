@props(['name', 'label', 'value' => '', 'maxlength' => null, 'rows' => 4, 'hint' => null, 'required' => false])
@php
    $id = $attributes->get('id', $name);
    $hasError = $errors->has($name);
    $describedBy = trim(($hint ? "{$id}-hint " : '') . ($hasError ? "{$id}-error" : ''));
@endphp

<div x-data="{ count: {{ mb_strlen(old($name, $value)) }} }" {{ $attributes->only('class') }}>
    <label for="{{ $id }}" class="text-label-md text-ink-strong">
        {{ $label }}
        @if ($required)<span class="text-danger-700" aria-hidden="true">*</span>@endif
    </label>

    @if ($hint)
        <p id="{{ $id }}-hint" class="mt-0.5 text-caption text-ink-muted">{{ $hint }}</p>
    @endif

    <textarea id="{{ $id }}" name="{{ $name }}" rows="{{ $rows }}"
        @if ($maxlength) maxlength="{{ $maxlength }}" @endif
        @if ($required) required @endif
        @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
        @if ($hasError) aria-invalid="true" @endif
        @input="count = $event.target.value.length"
        {{ $attributes->except(['class', 'id']) }}
        @class([
            'mt-1.5 w-full rounded-md border bg-surface px-3 py-2 text-body-md text-ink placeholder:text-ink-muted',
            'focus-visible:outline-2 focus-visible:outline-offset-1',
            'border-line-strong focus-visible:outline-brand' => ! $hasError,
            'border-danger-600 focus-visible:outline-danger-600' => $hasError,
        ])>{{ old($name, $value) }}</textarea>

    <div class="mt-1 flex items-start justify-between gap-3">
        @if ($hasError)
            <p id="{{ $id }}-error" class="text-caption text-danger-700">{{ $errors->first($name) }}</p>
        @endif
        @if ($maxlength)
            <p class="ml-auto shrink-0 text-caption text-ink-muted" aria-hidden="true">
                <span x-text="count"></span>/{{ $maxlength }}
            </p>
        @endif
    </div>
</div>