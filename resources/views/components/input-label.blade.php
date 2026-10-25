@props(['value', 'required' => false])

<label {{ $attributes->merge(['class' => 'block text-sm font-medium text-slate-700 mb-1.5']) }}>
    <span>{{ $value ?? $slot }}</span>
    @if ($required)
        <span class="text-rose-500 font-bold ml-0.5" title="Wajib diisi">*</span>
    @endif
</label>

