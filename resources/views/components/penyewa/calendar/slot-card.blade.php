@props([
    'id'         => '',
    'dateShort'  => '01 Nov',
    'fullDate'   => '',
    'timeLabel'  => '06:00 - 08:00',
    'timeStart'  => '',
    'timeEnd'    => '',
    'price'      => 150000,
    'status'     => 'available', // available | booked | pending | past | selected
    'courtName'  => 'Lapangan',
])

@php
    $isBooked = $status === 'booked';
    $isPending = $status === 'pending';
    $isPast = $status === 'past';
    $isSelected = $status === 'selected';
    $isAvailable = $status === 'available';

    $startTime = !empty($timeStart) ? $timeStart : trim(explode('-', $timeLabel)[0] ?? '');
    $endTime = !empty($timeEnd) ? $timeEnd : trim(explode('-', $timeLabel)[1] ?? '');

    $formattedPrice = 'Rp ' . number_format($price, 0, ',', '.');

    // Kelas tampilan kartu berdasarkan status
    if ($isBooked) {
        $containerClasses = 'bg-slate-50/80 border-slate-200/70 opacity-60 cursor-not-allowed';
    } elseif ($isPending) {
        $containerClasses = 'bg-amber-50/70 border-amber-300/80 text-amber-950 cursor-not-allowed ring-1 ring-amber-400/20';
    } elseif ($isPast) {
        $containerClasses = 'bg-slate-100/60 border-slate-200/60 opacity-40 cursor-not-allowed';
    } elseif ($isSelected) {
        $containerClasses = 'border-2 border-[#0B7B69] bg-emerald-50/50 shadow-xs ring-2 ring-emerald-500/20 cursor-pointer';
    } else {
        $containerClasses = 'bg-white border-slate-200/90 hover:border-emerald-500/60 hover:shadow-xs cursor-pointer';
    }
@endphp

<div
    class="slot-card group relative rounded-2xl border p-4 sm:p-5 transition-all duration-200 flex flex-col justify-center items-center text-center gap-1.5 select-none {{ $containerClasses }}"
    data-slot-card
    data-slot-id="{{ $id }}"
    data-date="{{ $dateShort }}"
    data-full-date="{{ $fullDate }}"
    data-time="{{ $timeLabel }}"
    data-time-start="{{ $startTime }}"
    data-time-end="{{ $endTime }}"
    data-price="{{ $price }}"
    data-court="{{ $courtName }}"
    data-status="{{ $status }}"
>
    <!-- 1. Jam -->
    <p class="text-xs sm:text-sm font-extrabold tracking-tight leading-snug {{ $isPast ? 'text-slate-400' : ($isBooked ? 'text-slate-500' : ($isPending ? 'text-amber-950' : 'text-slate-800')) }}">
        {{ $timeLabel }}
    </p>

    <!-- 2. Harga -->
    <p class="text-sm sm:text-base font-black tabular-nums leading-tight {{ $isPast ? 'text-slate-400' : ($isBooked ? 'text-slate-400' : ($isPending ? 'text-amber-900' : 'text-slate-900')) }}">
        {{ $formattedPrice }}
    </p>

    <!-- 3. Available / Status Badge -->
    <div class="pt-0.5">
        @if ($isBooked)
            <span class="slot-status-label inline-flex items-center gap-1 text-xs font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md">
                Booked
            </span>
        @elseif ($isPending)
            <span class="slot-status-label inline-flex items-center gap-1 text-xs font-bold text-amber-700 bg-amber-100 px-2 py-0.5 rounded-md">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                Ditahan
            </span>
        @elseif ($isPast)
            <span class="slot-status-label inline-flex items-center gap-1 text-xs font-medium text-slate-400 bg-slate-200/60 px-2 py-0.5 rounded-md">
                Lewat
            </span>
        @elseif ($isSelected)
            <span class="slot-status-label inline-flex items-center gap-1 text-xs font-bold text-[#0B7B69] bg-emerald-100/70 px-2 py-0.5 rounded-md">
                Dipilih Anda
            </span>
        @else
            <span class="slot-status-label inline-flex items-center gap-1 text-xs font-bold text-[#10B981] bg-emerald-50 px-2 py-0.5 rounded-md">
                Available
            </span>
        @endif
    </div>
</div>
