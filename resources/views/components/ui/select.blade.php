@props(['name', 'label', 'options', 'hint' => null, 'required' => false])
@php
    $id = $attributes->get('id', str_replace('.', '-', $name));
    $hasError = $errors->has($name);
@endphp

<div {{ $attributes->only('class') }}>
    <label for="{{ $id }}" class="text-label-md text-ink-strong">
        {{ $label }}@if ($required)<span class="text-danger-700" aria-hidden="true"> *</span>@endif
    </label>
    @if ($hint)
        <p id="{{ $id }}-hint" class="mt-0.5 text-caption text-ink-muted">{{ $hint }}</p>
    @endif

    <select id="{{ $id }}" name="{{ $name }}"
            @if ($hasError) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif
            {{ $attributes->except(['class', 'id']) }}
            @class([
                'mt-1.5 min-h-11 w-full rounded-md border bg-surface px-3 text-body-md text-ink lg:min-h-10',
                'focus-visible:outline-2 focus-visible:outline-offset-1',
                'border-line-strong focus-visible:outline-brand' => ! $hasError,
                'border-danger-600 focus-visible:outline-danger-600' => $hasError,
            ])>
        @foreach ($options as $value => $text)
            <option value="{{ $value }}">{{ $text }}</option>
        @endforeach
    </select>

    @if ($hasError)
        <p id="{{ $id }}-error" class="mt-1 text-caption text-danger-700">{{ $errors->first($name) }}</p>
    @endif
</div>