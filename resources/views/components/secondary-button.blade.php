@props(['type' => 'button'])

<button
    type="{{ $type }}"
    {{ $attributes->merge([
        'class' => 'inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 active:bg-slate-100 px-4 py-2.5 text-sm font-medium text-slate-700 shadow-xs transition duration-150 ease-in-out min-h-[44px] cursor-pointer disabled:opacity-50'
    ]) }}
>
    {{ $slot }}
</button>

