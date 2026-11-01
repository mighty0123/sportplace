@props([
    'selectedDate'  => 'Kamis, 24 Okt 2024',
    'selectedSlots' => [],
    'serviceFee'    => 2500,
    'rentalTotal'   => 300000,
    'grandTotal'    => 302500,
    'checkoutUrl'   => '#',
])

<div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-xs sticky top-24 space-y-4">
    <!-- Header: Title, Date, & Counter Badge -->
    <div class="flex items-start justify-between gap-3 border-b border-slate-100 pb-4">
        <div>
            <h3 class="font-bold text-slate-900 text-base leading-tight">
                Detail Booking
            </h3>
            <p class="text-xs text-slate-500 mt-1" id="summary-date">
                {{ $selectedDate }}
            </p>
        </div>
        <span
            id="summary-count-badge"
            class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80 shrink-0"
        >
            <span id="summary-count-number">{{ count($selectedSlots) }}</span> Slot Dipilih
        </span>
    </div>

    <!-- Selected Slots List -->
    <div id="selected-slots-container" class="space-y-2.5">
        @forelse ($selectedSlots as $slot)
            <div
                class="slot-summary-item bg-slate-50 border border-slate-100 rounded-xl p-3 flex items-start justify-between gap-3 transition hover:border-slate-200"
                data-summary-slot-id="{{ $slot['id'] }}"
                data-price="{{ $slot['price'] }}"
            >
                <div class="min-w-0">
                    <div class="flex items-center gap-1.5 text-xs font-bold text-slate-800">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                        <span class="truncate">{{ $slot['court_name'] }}</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        {{ $slot['time'] }}
                    </p>
                    <p class="text-xs font-bold text-[#0B7B69] mt-1 tabular-nums">
                        Rp {{ number_format($slot['price'], 0, ',', '.') }}
                    </p>
                </div>

                <button
                    type="button"
                    class="remove-slot-btn text-slate-400 hover:text-rose-500 p-1 rounded-lg hover:bg-slate-100 transition"
                    data-remove-slot-id="{{ $slot['id'] }}"
                    title="Hapus slot"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        @empty
            <div id="empty-slots-placeholder" class="py-8 text-center text-xs text-slate-400">
                Belum ada slot yang dipilih. Klik salah satu slot hijau di tabel untuk memilih.
            </div>
        @endforelse
    </div>

    <!-- Empty Placeholder (Hidden by default when slots exist) -->
    <div id="empty-slots-placeholder" class="hidden py-6 text-center text-xs text-slate-400 border border-dashed border-slate-200 rounded-xl p-4">
        Belum ada slot yang dipilih. Silakan pilih slot yang tersedia.
    </div>

    <!-- Cost Breakdown -->
    <div class="pt-3 border-t border-slate-100 space-y-2 text-xs">
        <div class="flex items-center justify-between text-slate-600">
            <span id="rental-duration-label">Sewa Lapangan (<span id="rental-hours-count">{{ count($selectedSlots) }}</span> Jam)</span>
            <span class="font-semibold text-slate-800 tabular-nums" id="rental-total-label">
                Rp {{ number_format($rentalTotal, 0, ',', '.') }}
            </span>
        </div>

        <div class="flex items-center justify-between text-slate-600">
            <span>Biaya Layanan</span>
            <span class="font-semibold text-slate-800 tabular-nums">
                Rp {{ number_format($serviceFee, 0, ',', '.') }}
            </span>
        </div>

        <div class="border-t border-slate-100 pt-3 flex items-center justify-between text-sm">
            <span class="font-bold text-slate-900">Total Estimasi</span>
            <span class="text-lg sm:text-xl font-black text-[#0B7B69] tabular-nums" id="grand-total-label">
                Rp {{ number_format($grandTotal, 0, ',', '.') }}
            </span>
        </div>
    </div>

    <!-- Promo Voucher Info Banner -->
    <div class="flex items-center gap-2 p-3 rounded-xl bg-amber-50/70 border border-amber-200/70 text-xs text-amber-900">
        <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
        </svg>
        <span class="leading-snug">Kode promo dapat digunakan di checkout</span>
    </div>

    <!-- CTA Button -->
    <div>
        <a
            href="{{ $checkoutUrl }}"
            id="btn-checkout"
            class="w-full py-3 px-4 rounded-xl bg-[#0B7B69] hover:bg-[#086354] active:scale-[0.99] text-white text-xs sm:text-sm font-bold flex items-center justify-center gap-2 shadow-xs transition duration-150"
        >
            <span>Lanjut ke Pembayaran</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
            </svg>
        </a>

        <!-- Hold Reservation Notice -->
        <div class="flex items-center justify-center gap-1.5 text-[11px] text-slate-400 mt-2.5">
            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" stroke-width="1.8"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2"/>
            </svg>
            <span>Slot ditahan selama 15 menit</span>
        </div>
    </div>
</div>
