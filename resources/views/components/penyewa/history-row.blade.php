@props([
    'sport'         => 'FUTSAL',
    'venueName'     => 'Nama Venue',
    'fieldName'     => 'Lapangan',
    'bookingCode'   => '#SP-00000',
    'location'      => 'Lokasi',
    'date'          => 'Tanggal',
    'time'          => '00:00 - 00:00 WIB (2 Jam)',
    'price'         => 0,
    'priceSub'      => null,
    'priceSubType'  => 'slate', // amber | emerald | slate | rose
    'status'        => 'confirmed', // pending | confirmed | completed | cancelled
    'actionType'    => 'detail', // pay | detail | review
    'detailUrl'     => '#',
    'payUrl'        => '#',
    'reviewUrl'     => '#',
    'imageSrc'      => null,
])

@php
    $sportUpper = strtoupper($sport);
    $isCancelled = strtolower($status) === 'cancelled';

    // Image fallback per sport
    $defaultImages = [
        'FUTSAL'    => asset('images/courts/futsal.jpg'),
        'BADMINTON' => asset('images/courts/badminton.jpg'),
        'TENIS'     => asset('images/courts/tennis.jpg'),
        'BASKET'    => asset('images/courts/basketball.jpg'),
    ];
    $courtImage = $imageSrc ?? ($defaultImages[$sportUpper] ?? asset('images/courts/futsal.jpg'));

    // Sport badge color tokens
    $sportBadges = [
        'TENIS'     => 'bg-sky-50 text-sky-700 border-sky-200/70',
        'FUTSAL'    => 'bg-emerald-50 text-emerald-700 border-emerald-200/70',
        'BADMINTON' => 'bg-indigo-50 text-indigo-700 border-indigo-200/70',
        'BASKET'    => 'bg-orange-50 text-orange-700 border-orange-200/70',
    ];
    $sportBadgeClass = $sportBadges[$sportUpper] ?? 'bg-slate-50 text-slate-700 border-slate-200/70';

    // Sub-price text color
    $priceSubClasses = [
        'amber'   => 'text-amber-700 font-semibold',
        'emerald' => 'text-emerald-700 font-medium',
        'rose'    => 'text-rose-600 font-semibold',
        'slate'   => 'text-slate-400 font-normal',
    ];
    $priceSubClass = $priceSubClasses[$priceSubType] ?? 'text-slate-400 font-normal';
@endphp

<tr class="border-b border-slate-100 hover:bg-slate-50/70 transition-colors duration-150 group">
    <!-- 1. LAPANGAN (Venue, Field, Code, Location, Sport) -->
    <td class="py-4 px-5 sm:px-6 align-middle">
        <div class="flex items-center gap-3.5 sm:gap-4">
            <!-- Court Image Thumbnail -->
            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl overflow-hidden shrink-0 ring-1 ring-slate-200/80 bg-slate-100">
                <img
                    src="{{ $courtImage }}"
                    alt="{{ $venueName }}"
                    class="w-full h-full object-cover transition-transform duration-200 group-hover:scale-105 {{ $isCancelled ? 'grayscale opacity-70' : '' }}"
                    loading="lazy"
                >
            </div>

            <!-- Title & Metadata -->
            <div class="min-w-0">
                <div class="flex items-center gap-2 mb-1">
                    <span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase tracking-wide border {{ $sportBadgeClass }}">
                        {{ $sportUpper }}
                    </span>
                </div>
                <h4 class="text-sm font-bold text-slate-900 group-hover:text-[#0B7B69] transition-colors leading-snug truncate max-w-xs sm:max-w-sm md:max-w-md {{ $isCancelled ? 'line-through text-slate-500' : '' }}" title="{{ $venueName }} - {{ $fieldName }}">
                    {{ $venueName }} - {{ $fieldName }}
                </h4>
                <p class="text-xs text-slate-400 mt-0.5 truncate">
                    <span class="font-medium text-slate-500">{{ $bookingCode }}</span> • {{ $location }}
                </p>
            </div>
        </div>
    </td>

    <!-- 2. TANGGAL & JAM -->
    <td class="py-4 px-4 sm:px-5 align-middle whitespace-nowrap">
        <div class="text-sm font-semibold {{ $isCancelled ? 'text-slate-400' : 'text-slate-900' }}">
            {{ $date }}
        </div>
        <div class="text-xs text-slate-500 mt-0.5">
            {{ $time }}
        </div>
    </td>

    <!-- 3. TOTAL HARGA -->
    <td class="py-4 px-4 sm:px-5 align-middle whitespace-nowrap">
        <div class="text-sm font-bold tabular-nums {{ $isCancelled ? 'line-through text-slate-400' : 'text-slate-900' }}">
            Rp {{ number_format($price, 0, ',', '.') }}
        </div>
        @if ($priceSub)
            <div class="text-xs mt-0.5 {{ $priceSubClass }}">
                {{ $priceSub }}
            </div>
        @endif
    </td>

    <!-- 4. STATUS -->
    <td class="py-4 px-4 sm:px-5 align-middle whitespace-nowrap">
        <x-penyewa.booking-status-badge :status="$status" />
    </td>

    <!-- 5. AKSI -->
    <td class="py-4 px-5 sm:px-6 align-middle text-right whitespace-nowrap">
        @if ($actionType === 'pay' || $status === 'pending')
            <a href="{{ $payUrl }}"
               class="inline-flex items-center justify-center px-4 py-1.5 rounded-lg bg-[#07362B] hover:bg-[#0B7B69] active:scale-95 text-white text-xs font-semibold shadow-xs transition duration-150 min-h-[34px]">
                Bayar
            </a>
        @elseif ($actionType === 'review')
            <a href="{{ $reviewUrl }}"
               class="inline-flex items-center justify-center px-3.5 py-1.5 text-xs font-bold text-[#0B7B69] hover:text-[#086354] hover:underline active:scale-95 transition duration-150 min-h-[34px]">
                Ulas
            </a>
        @else
            <a href="{{ $detailUrl }}"
               class="inline-flex items-center justify-center px-3.5 py-1.5 rounded-lg bg-white border border-slate-200 hover:bg-slate-50 hover:text-slate-900 active:scale-95 text-slate-600 text-xs font-medium shadow-2xs transition duration-150 min-h-[34px]">
                Detail
            </a>
        @endif
    </td>
</tr>
