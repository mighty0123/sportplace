@props([
    'id'           => 1,
    'venueName'    => 'Nama Venue',
    'fieldName'    => 'Nama Lapangan',
    'sport'        => 'FUTSAL',
    'courtType'    => 'Indoor Vinyl Mat',
    'location'     => 'Lokasi',
    'rating'       => '4.8',
    'reviewsCount' => 128,
    'pricePerHour' => 150000,
    'facilities'   => [],
    'openHours'    => '08:00 - 23:00',
    'imageSrc'     => null,
    'bookingUrl'   => null,
])

@php
    $sportUpper = strtoupper($sport);
    $defaultImages = [
        'FUTSAL'    => asset('images/courts/futsal.jpg'),
        'BADMINTON' => asset('images/courts/badminton.jpg'),
        'TENIS'     => asset('images/courts/tennis.jpg'),
        'BASKET'    => asset('images/courts/basketball.jpg'),
    ];
    $courtImage = $imageSrc ?? ($defaultImages[$sportUpper] ?? asset('images/courts/futsal.jpg'));
    $url = $bookingUrl ?? url('/penyewa/booking/' . $id);

    // Sport badge styling
    $sportBadges = [
        'FUTSAL'    => 'bg-[#FACC15] text-slate-900',
        'BADMINTON' => 'bg-indigo-600 text-white',
        'TENIS'     => 'bg-sky-500 text-white',
        'BASKET'    => 'bg-orange-500 text-white',
    ];
    $sportBadgeClass = $sportBadges[$sportUpper] ?? 'bg-emerald-600 text-white';
@endphp

<div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs hover:shadow-md transition-all duration-200 overflow-hidden flex flex-col group">
    <!-- Image Header with Badges -->
    <div class="relative h-48 sm:h-52 overflow-hidden bg-slate-900 shrink-0">
        <a href="{{ $url }}" class="block w-full h-full">
            <img
                src="{{ $courtImage }}"
                alt="{{ $venueName }} - {{ $fieldName }}"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                loading="lazy"
            >
        </a>

        <!-- Dark Gradient Vignette for Readability -->
        <div class="absolute inset-x-0 top-0 h-16 bg-gradient-to-b from-black/50 to-transparent pointer-events-none"></div>

        <!-- Sport Badge (Top Left) -->
        <span class="absolute top-3 left-3 px-2.5 py-1 rounded-md text-[10px] font-extrabold uppercase tracking-wide {{ $sportBadgeClass }} shadow-xs">
            {{ $sportUpper }}
        </span>

        <!-- Rating Pill (Top Right) -->
        <div class="absolute top-3 right-3 inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-bold bg-white/95 text-slate-900 shadow-xs backdrop-blur-xs">
            <svg class="w-3.5 h-3.5 text-amber-500 fill-current" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
            </svg>
            <span>{{ $rating }}</span>
            <span class="text-slate-400 font-medium text-[11px]">({{ $reviewsCount }})</span>
        </div>
    </div>

    <!-- Body Information -->
    <div class="p-4 sm:p-5 flex-1 flex flex-col justify-between gap-4">
        <div class="space-y-2">
            <!-- Surface/Type Tag -->
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded-md text-[11px] font-medium bg-slate-100 text-slate-600 border border-slate-200/60">
                    {{ $courtType }}
                </span>
                <span class="text-xs text-slate-400">• {{ $openHours }}</span>
            </div>

            <!-- Title -->
            <h3 class="text-base font-bold text-slate-900 leading-snug group-hover:text-[#0B7B69] transition-colors line-clamp-1" title="{{ $venueName }} - {{ $fieldName }}">
                <a href="{{ $url }}">
                    {{ $venueName }} - {{ $fieldName }}
                </a>
            </h3>

            <!-- Location Line -->
            <div class="flex items-center gap-1.5 text-xs text-slate-500">
                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="truncate">{{ $location }}</span>
            </div>

            <!-- Facilities Tags -->
            @if (!empty($facilities))
                <div class="flex flex-wrap gap-1.5 pt-1">
                    @foreach (array_slice($facilities, 0, 3) as $fac)
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-medium bg-emerald-50/60 text-[#0B7B69] border border-emerald-100">
                            {{ $fac }}
                        </span>
                    @endforeach
                    @if (count($facilities) > 3)
                        <span class="px-1.5 py-0.5 rounded-md text-[10px] font-medium text-slate-400">
                            +{{ count($facilities) - 3 }}
                        </span>
                    @endif
                </div>
            @endif
        </div>

        <!-- Footer: Pricing & Action Button -->
        <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-3">
            <div>
                <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-medium">Mulai dari</span>
                <div class="text-base sm:text-lg font-black text-[#0B7B69] tabular-nums leading-tight">
                    Rp {{ number_format($pricePerHour, 0, ',', '.') }}
                    <span class="text-xs text-slate-400 font-normal">/ jam</span>
                </div>
            </div>

            <a
                href="{{ $url }}"
                class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-[#0B7B69] hover:bg-[#086354] active:scale-95 text-white text-xs font-bold shadow-xs transition duration-150 shrink-0 gap-1"
            >
                <span>Lihat Jadwal & Booking</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    </div>
</div>
