@props([
    'sport'       => 'FUTSAL',     // FUTSAL | BADMINTON | TENIS | BASKET
    'status'      => 'confirmed',  // confirmed | ongoing | pending | cancelled
    'venueName'   => 'Nama Venue',
    'fieldName'   => 'Lapangan',
    'date'        => null,         // Carbon instance, or string
    'timeStart'   => '08:00',
    'timeEnd'     => '10:00',
    'location'    => 'Lokasi',
    'price'       => 0,
    'detailUrl'   => '#',
    'payUrl'      => null,
    'payDeadline' => null,         // e.g. "45 menit lagi"
    'imageSrc'    => null,
])

@php
    $sportUpper = strtoupper($sport);

    // Default realistic image fallback per sport category
    $defaultImages = [
        'FUTSAL'      => asset('images/courts/futsal.jpg'),
        'BADMINTON'   => asset('images/courts/badminton.jpg'),
        'TENIS'       => asset('images/courts/tennis.jpg'),
        'BASKET'      => asset('images/courts/basketball.jpg'),
        'MINI-SOCCER' => asset('images/courts/futsal.jpg'),
    ];

    $fallbackImg = $defaultImages[$sportUpper] ?? asset('images/courts/futsal.jpg');
    $cleanSrc = $imageSrc;
    if ($cleanSrc && str_contains($cleanSrc, 'storage/images/')) {
        $cleanSrc = str_replace('storage/images/', 'images/', $cleanSrc);
    }
    $courtImage = $cleanSrc ?: $fallbackImg;

    // Status config matching reference mockup
    $statusConfig = [
        'ongoing' => [
            'label' => 'Sedang Berlangsung',
            'pillClass' => 'bg-white/95 text-emerald-800 border border-emerald-200/80',
            'icon' => 'live-dot',
        ],
        'confirmed' => [
            'label' => 'Terkonfirmasi',
            'pillClass' => 'bg-white/95 text-emerald-800 border border-emerald-200/80',
            'icon' => 'check-circle',
        ],
        'pending' => [
            'label' => 'Pending Bayar',
            'pillClass' => 'bg-amber-50/95 text-amber-800 border border-amber-200/90',
            'icon' => 'clock',
        ],
        'cancelled' => [
            'label' => 'Dibatalkan',
            'pillClass' => 'bg-slate-100/95 text-slate-700 border border-slate-300',
            'icon' => 'x-circle',
        ],
    ];
    $st = $statusConfig[$status] ?? $statusConfig['confirmed'];

    // Sport badge style: High contrast readable pill
    $sportBadgeClass = match($sportUpper) {
        'BADMINTON', 'BASKET' => 'bg-slate-900/85 text-white',
        default => 'bg-white/95 text-slate-900 font-bold',
    };

    // Format date string
    if ($date instanceof \Carbon\Carbon) {
        $isToday = $date->isToday();
        $dateFormatted = $isToday
            ? 'Hari ini, ' . $date->translatedFormat('d M Y')
            : $date->translatedFormat('l, d M Y');
    } else {
        $dateString = (string)$date;
        $isToday = str_contains(strtolower($dateString), 'hari ini');
        $dateFormatted = $dateString;
    }
@endphp

<div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs hover:shadow-md transition-all duration-200 overflow-hidden flex flex-col group">
    <!-- Image Header Area with Badges -->
    <div class="relative h-44 sm:h-48 overflow-hidden bg-slate-900 shrink-0">
        <a href="{{ $detailUrl }}" class="block w-full h-full">
            <img
                src="{{ $courtImage }}"
                alt="{{ $venueName }} - {{ $fieldName }}"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                loading="lazy"
                onerror="this.onerror=null; this.src='{{ $fallbackImg }}';"
            >
        </a>

        <!-- Subtle Dark Vignette for Badges Legibility -->
        <div class="absolute inset-x-0 top-0 h-16 bg-gradient-to-b from-black/40 to-transparent pointer-events-none"></div>

        <!-- Sport Pill (Top-Left) -->
        <span class="absolute top-3 left-3 px-2.5 py-1 rounded-md text-[10px] font-bold tracking-wider {{ $sportBadgeClass }} shadow-xs backdrop-blur-xs">
            {{ $sportUpper }}
        </span>

        <!-- Status Pill (Top-Right) -->
        <span class="absolute top-3 right-3 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-semibold {{ $st['pillClass'] }} shadow-xs backdrop-blur-xs">
            @if ($st['icon'] === 'live-dot')
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
            @elseif ($st['icon'] === 'check-circle')
                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke-width="1.8"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12l2.5 2.5L16 9"/>
                </svg>
            @elseif ($st['icon'] === 'clock')
                <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            @endif
            <span>{{ $st['label'] }}</span>
        </span>
    </div>

    <!-- Card Body Area -->
    <div class="p-4 sm:p-5 flex flex-col flex-1 justify-between gap-3">
        <div>
            <!-- Venue & Field Title -->
            <h3 class="font-bold text-slate-900 text-sm sm:text-base leading-snug line-clamp-1 group-hover:text-[#0B7B69] transition-colors" title="{{ $venueName }} - {{ $fieldName }}">
                <a href="{{ $detailUrl }}">
                    {{ $venueName }} - {{ $fieldName }}
                </a>
            </h3>

            <!-- Dynamic Metadata: Time or Payment Alert -->
            <div class="mt-2.5 space-y-1.5">
                @if ($status === 'pending' && $payDeadline)
                    <!-- Urgent Payment Warning -->
                    <div class="flex items-center gap-1.5 text-xs font-semibold text-amber-700">
                        <svg class="w-3.5 h-3.5 shrink-0 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Batas bayar: {{ $payDeadline }}</span>
                    </div>
                @else
                    <!-- Date & Time Info -->
                    <div class="flex items-center gap-1.5 text-xs {{ $isToday ? 'text-emerald-700 font-semibold' : 'text-slate-600' }}">
                        <svg class="w-3.5 h-3.5 shrink-0 {{ $isToday ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="truncate">
                            {{ $dateFormatted }} • {{ $timeStart }} - {{ $timeEnd }} WIB
                        </span>
                    </div>
                @endif

                <!-- Location Line -->
                <div class="flex items-center gap-1.5 text-xs text-slate-400">
                    <svg class="w-3.5 h-3.5 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="truncate">{{ $location }}</span>
                </div>
            </div>
        </div>

        <!-- Card Footer: Total Cost & Action Button -->
        <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-3 mt-1">
            <div>
                <span class="text-[10px] text-slate-400 font-medium uppercase tracking-wider block leading-none">Total Biaya</span>
                <span class="text-base sm:text-lg font-bold text-slate-900 tabular-nums leading-tight">
                    Rp {{ number_format($price, 0, ',', '.') }}
                </span>
            </div>

            @if ($status === 'pending' && $payUrl)
                <a href="{{ $payUrl }}"
                   class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-[#0B7B69] hover:bg-[#086354] active:scale-95 text-white text-xs font-semibold shadow-xs transition duration-150 shrink-0">
                    Selesaikan Bayar
                </a>
            @else
                <a href="{{ $detailUrl }}"
                   class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-white border border-[#0B7B69] text-[#0B7B69] hover:bg-emerald-50 active:scale-95 text-xs font-semibold transition duration-150 shrink-0">
                    Lihat Detail
                </a>
            @endif
        </div>
    </div>
</div>
