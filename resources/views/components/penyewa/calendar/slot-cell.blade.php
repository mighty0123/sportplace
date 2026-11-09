@props([
    'item' => [],
    'time' => '',
])

@php
    $status = $item['status'] ?? 'available';
    $slotId = $item['id'] ?? uniqid('slot_');
    $price = $item['price'] ?? 0;
    $court = $item['court'] ?? '';
    $label = $item['label'] ?? 'Tersedia';
    $title = $item['title'] ?? '';
    $subtitle = $item['subtitle'] ?? '';
@endphp

@if ($status === 'available')
    <button
        type="button"
        data-slot-cell
        data-slot-id="{{ $slotId }}"
        data-status="available"
        data-price="{{ $price }}"
        data-court="{{ $court }}"
        data-time="{{ $time }}"
        class="slot-btn w-full py-2.5 px-2 rounded-xl text-center bg-emerald-50/60 hover:bg-emerald-100/80 border border-emerald-200/60 text-emerald-800 transition duration-150 shadow-2xs group focus:outline-none focus:ring-2 focus:ring-emerald-500/30"
    >
        <span class="block text-xs font-bold text-emerald-700 group-hover:text-emerald-800 leading-tight">
            {{ $label }}
        </span>
        <span class="block text-[11px] font-medium text-emerald-600/90 mt-0.5 tabular-nums">
            Rp {{ number_format($price, 0, ',', '.') }}
        </span>
    </button>

@elseif ($status === 'selected')
    <button
        type="button"
        data-slot-cell
        data-slot-id="{{ $slotId }}"
        data-status="selected"
        data-price="{{ $price }}"
        data-court="{{ $court }}"
        data-time="{{ $time }}"
        class="slot-btn w-full py-2.5 px-2 rounded-xl text-center bg-[#0B7B69] hover:bg-[#086354] border border-[#0B7B69] text-white transition duration-150 shadow-xs focus:outline-none focus:ring-2 focus:ring-emerald-400/40"
    >
        <span class="flex items-center justify-center gap-1 text-xs font-bold text-white leading-tight">
            <svg class="w-3.5 h-3.5 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
            <span>Dipilih Anda</span>
        </span>
        <span class="block text-[11px] font-medium text-emerald-100 mt-0.5 tabular-nums">
            Rp {{ number_format($price, 0, ',', '.') }}
        </span>
    </button>

@elseif ($status === 'booked')
    <div class="w-full py-2.5 px-2 rounded-xl text-center bg-slate-100/90 border border-slate-200/70 select-none">
        <span class="block text-xs font-bold text-slate-700 truncate leading-tight" title="{{ $title }}">
            {{ $title }}
        </span>
        <span class="block text-[10px] text-slate-400 mt-0.5">
            {{ $subtitle ?: 'Terisi' }}
        </span>
    </div>

@elseif ($status === 'maintenance')
    <div class="w-full py-2.5 px-2 rounded-xl text-center bg-amber-50/90 border border-amber-200/80 relative overflow-hidden select-none bg-[linear-gradient(45deg,#fef3c7_25%,transparent_25%,transparent_50%,#fef3c7_50%,#fef3c7_75%,transparent_75%,transparent)] [background-size:10px_10px]">
        <span class="block text-xs font-bold text-amber-900 truncate leading-tight" title="{{ $title }}">
            {{ $title }}
        </span>
        <span class="block text-[10px] font-medium text-amber-700 mt-0.5 truncate" title="{{ $subtitle }}">
            {{ $subtitle ?: 'Perawatan' }}
        </span>
    </div>

@elseif ($status === 'closed')
    <div class="w-full py-2.5 px-2 rounded-xl text-center bg-slate-50 border border-slate-200/50 select-none opacity-80">
        <span class="block text-xs font-semibold text-slate-400 leading-tight">
            {{ $title ?: 'Tutup' }}
        </span>
        <span class="block text-[10px] text-slate-400 mt-0.5">
            {{ $subtitle ?: 'Jadwal Selesai' }}
        </span>
    </div>
@endif
