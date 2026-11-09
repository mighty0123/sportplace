@props([
    'label' => '',
    'value' => '-',
])

<div class="bg-slate-50/80 border border-slate-100 hover:border-slate-200/90 rounded-xl p-3.5 sm:p-4 transition duration-150">
    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider leading-none">
        {{ $label }}
    </span>
    <span class="block text-xs sm:text-sm font-semibold text-slate-800 mt-1.5 leading-snug truncate" title="{{ $value }}">
        {{ $value }}
    </span>
</div>
