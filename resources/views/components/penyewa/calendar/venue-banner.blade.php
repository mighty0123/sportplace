@props([
    'sport'        => 'FUTSAL',
    'courtType'    => 'Indoor Vinyl Mat',
    'rating'       => '4.8',
    'reviewsCount' => 128,
    'name'         => 'Viva Arena Futsal - Lapangan Vinyl A',
    'location'     => 'Renon, Denpasar, Bali',
    'basePrice'    => 150000,
    'imageSrc'     => null,
])

@php
    $sportUpper = strtoupper($sport);
    $defaultImage = asset('images/courts/futsal.jpg');
    $courtImage = $imageSrc ?? $defaultImage;
@endphp

<div class="bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
    <!-- Left: Image Thumbnail & Venue Details -->
    <div class="flex items-start sm:items-center gap-4">
        <!-- Thumbnail with Sport Badge -->
        <div class="relative w-20 h-20 sm:w-24 sm:h-24 md:w-28 md:h-24 rounded-xl overflow-hidden shrink-0 ring-1 ring-slate-200/80 bg-slate-100">
            <img
                src="{{ $courtImage }}"
                alt="{{ $name }}"
                class="w-full h-full object-cover"
                loading="lazy"
            >
            <span class="absolute top-2 left-2 px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase tracking-wide bg-[#FACC15] text-slate-900 shadow-xs">
                {{ $sportUpper }}
            </span>
        </div>

        <!-- Venue Metadata & Title -->
        <div class="min-w-0">
            <div class="flex flex-wrap items-center gap-2 mb-1">
                <!-- Court Material Badge -->
                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200/60">
                    {{ $courtType }}
                </span>

                <!-- Rating -->
                <div class="flex items-center gap-1 text-xs text-slate-600 font-medium">
                    <svg class="w-4 h-4 text-amber-400 fill-current" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    <span class="font-bold text-slate-800">{{ $rating }}</span>
                    <span class="text-slate-400">({{ $reviewsCount }} ulasan)</span>
                </div>
            </div>

            <!-- Title -->
            <h1 class="text-base sm:text-lg md:text-xl font-extrabold text-slate-900 leading-snug">
                {{ $name }}
            </h1>

            <!-- Location -->
            <div class="flex items-center gap-1.5 text-xs text-slate-500 mt-1">
                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span>{{ $location }}</span>
            </div>
        </div>
    </div>

    <!-- Right: Rental Tariff -->
    <div class="md:text-right shrink-0 pt-3 md:pt-0 border-t md:border-t-0 border-slate-100 flex md:flex-col justify-between items-center md:items-end">
        <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider block">
            TARIF SEWA
        </span>
        <div class="text-xs text-slate-500 flex items-baseline gap-1 mt-0.5">
            <span class="text-slate-400 font-medium">Mulai</span>
            <span class="text-xl sm:text-2xl font-black text-[#0B7B69] tabular-nums">
                Rp {{ number_format($basePrice, 0, ',', '.') }}
            </span>
            <span class="text-slate-400 text-xs">/ jam</span>
        </div>
    </div>
</div>
