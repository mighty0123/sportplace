@props([
    'id'         => '',
    'dateShort'  => '01 Nov',
    'fullDate'   => '',
    'timeLabel'  => '06:00 - 08:00',
    'price'      => 150000,
    'status'     => 'available', // available | booked | selected
    'courtName'  => 'Lapangan',
])

@php
    $isBooked = $status === 'booked';
    $isSelected = $status === 'selected';
    $formattedPrice = 'Rp ' . number_format($price, 0, ',', '.');
@endphp

<div
    class="slot-card group relative bg-white rounded-2xl border p-4 sm:p-5 transition-all duration-200 flex flex-col justify-center items-center text-center gap-1.5 select-none
           {{ $isBooked
                ? 'bg-slate-50/80 border-slate-200/70 opacity-60 cursor-not-allowed'
                : ($isSelected
                    ? 'border-2 border-[#0B7B69] bg-emerald-50/50 shadow-xs ring-2 ring-emerald-500/20 cursor-pointer'
                    : 'border-slate-200/90 hover:border-emerald-500/60 hover:shadow-xs cursor-pointer') }}"
    data-slot-card
    data-slot-id="{{ $id }}"
    data-date="{{ $dateShort }}"
    data-full-date="{{ $fullDate }}"
    data-time="{{ $timeLabel }}"
    data-price="{{ $price }}"
    data-court="{{ $courtName }}"
    data-status="{{ $status }}"
>
    <!-- 1. Jam -->
    <p class="text-xs sm:text-sm font-extrabold text-slate-800 tracking-tight leading-snug">
        {{ $timeLabel }}
    </p>

    <!-- 2. Harga -->
    <p class="text-sm sm:text-base font-black text-slate-900 tabular-nums leading-tight">
        {{ $formattedPrice }}
    </p>

    <!-- 3. Available / Status -->
    <div class="pt-0.5">
        @if ($isBooked)
            <span class="slot-status-label text-xs font-bold text-slate-400">
                Booked
            </span>
        @elseif ($isSelected)
            <span class="slot-status-label text-xs font-bold text-[#0B7B69]">
                Dipilih Anda
            </span>
        @else
            <span class="slot-status-label text-xs font-bold text-[#10B981]">
                Available
            </span>
        @endif
    </div>
</div>
