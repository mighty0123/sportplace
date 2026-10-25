@props([
    'disabled' => false,
    'hasError' => false,
])

<select
    @disabled($disabled)
    {{ $attributes->merge([
        'class' => 'w-full rounded-xl border bg-white px-3.5 py-2.5 text-sm text-slate-800 min-h-[44px] transition duration-150 ease-in-out focus:outline-hidden focus:ring-3 ' .
        ($hasError
            ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-500/20 text-rose-900'
            : 'border-slate-300 focus:border-[#0B7B69] focus:ring-[#0B7B69]/20')
    ]) }}
>
    {{ $slot }}
</select>

