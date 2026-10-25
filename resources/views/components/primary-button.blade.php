@props(['type' => 'submit'])

<button
    type="{{ $type }}"
    {{ $attributes->merge([
        'class' => 'w-full inline-flex items-center justify-center gap-2 rounded-xl bg-[#0B7B69] hover:bg-[#086354] active:bg-[#064E43] px-5 py-3 text-sm font-semibold text-white shadow-xs transition duration-150 ease-in-out min-h-[44px] cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed'
    ]) }}
>
    {{ $slot }}
</button>

