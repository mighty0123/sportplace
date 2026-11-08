@props([
    'sport'         => 'futsal',
    'venueName'     => 'Nama Venue',
    'schedule'      => 'Jadwal',
    'price'         => 0,
    'paymentMethod' => 'QRIS',
    'statusLabel'   => 'Selesai Main',
])

<div class="p-3.5 sm:p-4 rounded-xl bg-slate-50/80 border border-slate-100 hover:border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4 transition duration-150">
    <!-- Left: Icon & Details -->
    <div class="flex items-center gap-3 min-w-0">
        <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-200/60 text-[#0B7B69] flex items-center justify-center shrink-0 shadow-2xs">
            @if (strtolower($sport) === 'futsal' || strtolower($sport) === 'mini-soccer')
                <!-- Futsal Ball SVG -->
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke-width="1.8"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 2a10 10 0 0110 10M12 2a10 10 0 00-10 10M12 2v20M2 12h20"/>
                </svg>
            @elseif (strtolower($sport) === 'badminton')
                <!-- Badminton Shuttle SVG -->
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 2l4 7H8l4-7zM9 13a3 3 0 106 0M12 16v6"/>
                </svg>
            @elseif (strtolower($sport) === 'basket')
                <!-- Basketball SVG -->
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke-width="1.8"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2 12h20M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/>
                </svg>
            @elseif (strtolower($sport) === 'tenis' || strtolower($sport) === 'tennis')
                <!-- Tennis SVG -->
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke-width="1.8"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M18.36 5.64a9 9 0 00-12.72 0M5.64 18.36a9 9 0 0012.72 0"/>
                </svg>
            @else
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke-width="1.8"/>
                </svg>
            @endif
        </div>

        <div class="min-w-0">
            <h4 class="text-xs sm:text-sm font-bold text-slate-800 leading-snug truncate" title="{{ $venueName }}">
                {{ $venueName }}
            </h4>
            <p class="text-xs text-slate-500 mt-0.5 truncate">
                {{ $schedule }}
            </p>
        </div>
    </div>

    <!-- Right: Pricing & Status Badge -->
    <div class="flex items-center justify-between sm:justify-end gap-3 sm:gap-4 shrink-0 border-t sm:border-t-0 pt-2 sm:pt-0 border-slate-100">
        <div class="sm:text-right">
            <span class="block text-xs sm:text-sm font-bold text-slate-900 tabular-nums">
                Rp {{ number_format($price, 0, ',', '.') }}
            </span>
            <span class="block text-[11px] text-slate-400">
                Lunas ({{ $paymentMethod }})
            </span>
        </div>

        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/80 shadow-2xs whitespace-nowrap">
            <svg class="w-3 h-3 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
            <span>{{ $statusLabel }}</span>
        </span>
    </div>
</div>
