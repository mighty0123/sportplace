@props(['options', 'model', 'label' => null])

<div role="group" @if ($label) aria-label="{{ $label }}" @endif
        {{ $attributes->class('flex w-full overflow-hidden rounded-md border border-line-strong md:inline-flex md:w-auto') }}>
    @foreach ($options as $value => $text)
        <button type="button"
                @click="{{ $model }} = @js($value)"
                :aria-pressed="{{ $model }} === @js($value)"
                :class="{{ $model }} === @js($value) ? 'bg-brand-soft text-primary-700' : 'text-ink-body hover:bg-surface-muted'"
                class="min-h-11 flex-1 px-3 text-label-md transition-colors motion-reduce:transition-none md:min-h-9 md:flex-none">
            {{ $text }}
        </button>
    @endforeach
</div>