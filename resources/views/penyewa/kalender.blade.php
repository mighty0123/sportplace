{{--
    Halaman Kalender & Jadwal Booking Lapangan — SportPlace
    Layout: Pemilihan Per Tanggal dengan Grid Kartu Slot Jam (Sesuai Referensi Gambar Pengguna)
    Style, Warna, dan Font mengikuti SportPlace Design System (theme.css)
--}}
<x-layouts.penyewa title="Jadwal {{ $field->nama_lapangan }} — SportPlace" active="lapangan">
<div class="space-y-6">

    {{-- =========================================================
         SECTION 0: Breadcrumb & Navigasi Kembali
    ========================================================= --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <nav class="flex items-center gap-2 text-xs font-medium text-slate-500">
            @auth
                <a href="{{ url('/penyewa/dashboard') }}" class="hover:text-[#0B7B69] transition">Dashboard</a>
            @else
                <a href="{{ url('/') }}" class="hover:text-[#0B7B69] transition">Beranda</a>
            @endauth
            <span>/</span>
            <a href="{{ url('/penyewa/lapangan') }}" class="hover:text-[#0B7B69] transition">Daftar Lapangan</a>
            <span>/</span>
            <a href="{{ url('/penyewa/lapangan/' . $field->id) }}" class="hover:text-[#0B7B69] transition truncate max-w-[160px]">{{ $field->nama_lapangan }}</a>
            <span>/</span>
            <span class="text-slate-900 font-bold">Jadwal Lapangan</span>
        </nav>

        <a
            href="{{ url('/penyewa/lapangan/' . $field->id) }}"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white border border-slate-200/90 hover:bg-slate-50 text-xs font-semibold text-slate-700 shadow-2xs transition shrink-0"
        >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali ke Detail Lapangan</span>
        </a>
    </div>

    {{-- =========================================================
         SECTION 1: Venue & Court Banner Card
    ========================================================= --}}
    <x-penyewa.calendar.venue-banner
        :sport="$venue['sport']"
        :court-type="$venue['court_type']"
        :rating="$venue['rating']"
        :reviews-count="$venue['reviews_count']"
        :name="$venue['name']"
        :location="$venue['location']"
        :base-price="$venue['base_price']"
        :image-src="$venue['image_src']"
    />

    {{-- =========================================================
         SECTION 2: Baris Kartu Tanggal (Date Strip Sesuai Mockup)
    ========================================================= --}}
    <div class="space-y-2">
        <div class="flex items-center justify-between">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                Pilih Tanggal Booking
            </h3>
            <span class="text-xs font-semibold text-[#0B7B69]">
                {{ $formattedSelectedDate }}
            </span>
        </div>

        <x-penyewa.calendar.date-strip
            :dates="$dates"
            :selected-date="$selectedDateStr"
        />
    </div>

    {{-- =========================================================
         SECTION 3: Status Legend & Info Jam Hari Terpilih
    ========================================================= --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h3 class="text-sm font-bold text-slate-900 leading-tight">
                Pilihan Jam untuk: <span class="text-[#0B7B69]">{{ $formattedSelectedDate }}</span>
            </h3>
            <p class="text-xs text-slate-500 mt-0.5">
                Klik kartu jam yang Anda inginkan untuk memilih slot sewa
            </p>
        </div>

        <!-- Legend Indicators -->
        <div class="flex flex-wrap items-center gap-3 sm:gap-4 text-xs font-semibold text-slate-600">
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-full bg-[#10B981] shrink-0"></span>
                <span>Available</span>
            </div>

            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-full bg-[#0B7B69] shrink-0 ring-2 ring-emerald-200"></span>
                <span>Dipilih Anda</span>
            </div>

            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-full bg-amber-500 shrink-0 ring-2 ring-amber-200"></span>
                <span>Ditahan</span>
            </div>

            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-full bg-slate-400 shrink-0"></span>
                <span>Booked</span>
            </div>

            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-full bg-slate-200 shrink-0"></span>
                <span>Lewat</span>
            </div>
        </div>
    </div>

    {{-- =========================================================
         SECTION 4: Grid Kartu Slot Jam (Hari yang Sama) & Sticky Summary
    ========================================================= --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        {{-- ---- Left 8 Cols: Grid Kartu Slot Jam ---- --}}
        <div class="lg:col-span-8 space-y-4">
            @if(count($timeSlots) > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3.5 sm:gap-4">
                    @foreach ($timeSlots as $slot)
                        <x-penyewa.calendar.slot-card
                            :id="$slot['id']"
                            :date-short="$slot['date_short']"
                            :full-date="$slot['full_date']"
                            :time-label="$slot['time_label']"
                            :time-start="$slot['time_start']"
                            :time-end="$slot['time_end']"
                            :price="$slot['price']"
                            :status="$slot['status']"
                            :court-name="$slot['court_name']"
                        />
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-2xl border border-slate-200/90 p-8 text-center space-y-2">
                    <p class="text-sm font-bold text-slate-700">Tidak ada jadwal operasional pada tanggal ini.</p>
                    <p class="text-xs text-slate-400">Silakan pilih tanggal lain di strip atas.</p>
                </div>
            @endif
        </div>

        {{-- ---- Right 4 Cols: Booking Summary (Sticky) ---- --}}
        <div class="lg:col-span-4 lg:sticky lg:top-24">
            <x-penyewa.calendar.booking-summary
                :selected-date="$formattedSelectedDate"
                :selected-slots="[]"
                :service-fee="$serviceFee"
                :rental-total="0"
                :grand-total="0"
                checkout-url="{{ route('penyewa.pembayaran.review', ['field_id' => $field->id, 'date' => $selectedDateStr]) }}"
            />
        </div>

    </div>

</div>

{{-- Interactive Slot Selection JavaScript --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const serviceFee = {{ $serviceFee }};
    const slotsContainer = document.getElementById('selected-slots-container');
    const emptyPlaceholder = document.getElementById('empty-slots-placeholder');
    const countBadge = document.getElementById('summary-count-number');
    const hoursCount = document.getElementById('rental-hours-count');
    const rentalTotalLabel = document.getElementById('rental-total-label');
    const grandTotalLabel = document.getElementById('grand-total-label');

    function formatRupiah(number) {
        return 'Rp ' + number.toLocaleString('id-ID');
    }

    function recalculateSummary() {
        const items = document.querySelectorAll('.slot-summary-item');
        const count = items.length;

        // Gather all selected cards and sort chronologically
        const selectedCards = Array.from(document.querySelectorAll('[data-slot-card][data-status="selected"]'));
        selectedCards.sort((a, b) => {
            const timeA = a.getAttribute('data-time') || '';
            const timeB = b.getAttribute('data-time') || '';
            return timeA.localeCompare(timeB);
        });

        let totalRental = 0;
        const slotPayloadItems = [];

        selectedCards.forEach(function (card) {
            const price = parseInt(card.getAttribute('data-price') || 0, 10);
            const time = card.getAttribute('data-time') || '';
            totalRental += price;
            if (time) {
                slotPayloadItems.push(time + ':' + price);
            }
        });

        const grandTotal = count > 0 ? (totalRental + serviceFee) : 0;

        if (countBadge) countBadge.textContent = count;
        if (hoursCount) hoursCount.textContent = count;
        if (rentalTotalLabel) rentalTotalLabel.textContent = formatRupiah(totalRental);
        if (grandTotalLabel) grandTotalLabel.textContent = formatRupiah(grandTotal);

        if (count === 0) {
            if (emptyPlaceholder) emptyPlaceholder.classList.remove('hidden');
        } else {
            if (emptyPlaceholder) emptyPlaceholder.classList.add('hidden');
        }

        // Dynamically update checkout button URL with exact field, date, and selected slots
        const btnCheckout = document.getElementById('btn-checkout');
        if (btnCheckout) {
            const baseUrl = "{{ route('penyewa.pembayaran.review') }}";
            const params = new URLSearchParams();
            params.set('field_id', "{{ $field->id }}");
            params.set('date', "{{ $selectedDateStr }}");

            if (slotPayloadItems.length > 0) {
                params.set('slots', slotPayloadItems.join('|'));
            }

            btnCheckout.href = baseUrl + '?' + params.toString();

            if (count === 0) {
                btnCheckout.classList.add('opacity-75');
            } else {
                btnCheckout.classList.remove('opacity-75');
            }
        }
    }

    // Guard button checkout agar tidak bisa checkout tanpa memilih minimal 1 slot
    const btnCheckout = document.getElementById('btn-checkout');
    if (btnCheckout) {
        btnCheckout.addEventListener('click', function (e) {
            const count = document.querySelectorAll('[data-slot-card][data-status="selected"]').length;
            if (count === 0) {
                e.preventDefault();
                alert('Silakan pilih minimal 1 slot jam bermain terlebih dahulu sebelum melanjutkan ke pembayaran.');
                return false;
            }
        });
    }

    // Fungsi penguncian real-time: mengunci slot langsung saat waktu sekarang >= jam_mulai
    function lockPastSlots() {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const todayStr = `${year}-${month}-${day}`;
        const nowMinutes = (now.getHours() * 60) + now.getMinutes();

        document.querySelectorAll('[data-slot-card]').forEach(function (card) {
            const fullDate = card.getAttribute('data-full-date');
            const timeStart = card.getAttribute('data-time-start') || (card.getAttribute('data-time') || '').split('-')[0].trim();
            if (!fullDate || !timeStart) return;

            let isPassed = false;
            if (fullDate < todayStr) {
                isPassed = true;
            } else if (fullDate === todayStr) {
                const parts = timeStart.split(':');
                const startMinutes = (parseInt(parts[0], 10) * 60) + parseInt(parts[1] || 0, 10);
                // Kunci langsung begitu lewat jam_mulai (tidak menunggu jam_selesai)
                if (nowMinutes >= startMinutes) {
                    isPassed = true;
                }
            }

            if (isPassed) {
                const currentStatus = card.getAttribute('data-status');
                if (currentStatus === 'selected') {
                    const slotId = card.getAttribute('data-slot-id');
                    const summaryItem = document.querySelector(`[data-summary-slot-id="${slotId}"]`);
                    if (summaryItem) summaryItem.remove();
                    recalculateSummary();
                }

                if (currentStatus !== 'booked') {
                    card.setAttribute('data-status', 'past');
                    card.className = 'slot-card group relative rounded-2xl border p-4 sm:p-5 transition-all duration-200 flex flex-col justify-center items-center text-center gap-1.5 select-none bg-slate-100/60 border-slate-200/60 opacity-40 cursor-not-allowed';

                    const timeEl = card.querySelector('p:first-child');
                    if (timeEl) {
                        timeEl.className = 'text-xs sm:text-sm font-extrabold tracking-tight leading-snug text-slate-400';
                    }
                    const priceEl = card.querySelector('p:nth-child(2)');
                    if (priceEl) {
                        priceEl.className = 'text-sm sm:text-base font-black tabular-nums leading-tight text-slate-400';
                    }
                    const statusLabel = card.querySelector('.slot-status-label');
                    if (statusLabel) {
                        statusLabel.textContent = 'Lewat';
                        statusLabel.className = 'slot-status-label inline-flex items-center gap-1 text-xs font-medium text-slate-400 bg-slate-200/60 px-2 py-0.5 rounded-md';
                    }
                }
            }
        });
    }

    // Jalankan penguncian saat halaman dibuka dan pantau tiap 10 detik
    lockPastSlots();
    setInterval(lockPastSlots, 10000);

    // Toggle Slot Card Selection
    document.querySelectorAll('[data-slot-card]').forEach(function (card) {
        card.addEventListener('click', function () {
            // Pastikan dicek real-time terlebih dahulu
            const fullDate = card.getAttribute('data-full-date');
            const timeStart = card.getAttribute('data-time-start') || (card.getAttribute('data-time') || '').split('-')[0].trim();
            if (fullDate && timeStart) {
                const now = new Date();
                const year = now.getFullYear();
                const month = String(now.getMonth() + 1).padStart(2, '0');
                const day = String(now.getDate()).padStart(2, '0');
                const todayStr = `${year}-${month}-${day}`;
                const nowMinutes = (now.getHours() * 60) + now.getMinutes();

                const parts = timeStart.split(':');
                const startMinutes = (parseInt(parts[0], 10) * 60) + parseInt(parts[1] || 0, 10);

                if (fullDate < todayStr || (fullDate === todayStr && nowMinutes >= startMinutes)) {
                    lockPastSlots();
                    alert('Slot jam ' + timeStart + ' sudah lewat jam mulai dan tidak dapat dipesan.');
                    return;
                }
            }

            const currentStatus = card.getAttribute('data-status');
            // Hanya slot dengan status 'available' atau 'selected' yang bisa diklik.
            // Slot 'booked', 'pending' (ditahan), dan 'past' (lewat) tidak dapat dipilih.
            if (currentStatus !== 'available' && currentStatus !== 'selected') return;

            const slotId = card.getAttribute('data-slot-id');
            const dateShort = card.getAttribute('data-date');
            const time = card.getAttribute('data-time');
            const price = parseInt(card.getAttribute('data-price') || 0, 10);
            const court = card.getAttribute('data-court') || 'Lapangan';
            const statusLabel = card.querySelector('.slot-status-label');

            if (currentStatus === 'available') {
                // Change to selected
                card.setAttribute('data-status', 'selected');
                card.classList.remove('bg-white', 'border-slate-200/90', 'hover:border-emerald-500/60', 'hover:shadow-xs');
                card.classList.add('border-2', 'border-[#0B7B69]', 'bg-emerald-50/50', 'shadow-xs', 'ring-2', 'ring-emerald-500/20');

                if (statusLabel) {
                    statusLabel.textContent = 'Dipilih Anda';
                    statusLabel.className = 'slot-status-label inline-flex items-center gap-1 text-xs font-bold text-[#0B7B69] bg-emerald-100/70 px-2 py-0.5 rounded-md';
                }

                // Add to summary container
                const summaryItem = document.createElement('div');
                summaryItem.className = 'slot-summary-item bg-slate-50 border border-slate-100 rounded-xl p-3 flex items-start justify-between gap-3 transition hover:border-slate-200';
                summaryItem.setAttribute('data-summary-slot-id', slotId);
                summaryItem.setAttribute('data-price', price);
                summaryItem.innerHTML = `
                    <div class="min-w-0">
                        <div class="flex items-center gap-1.5 text-xs font-bold text-slate-800">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                            <span class="truncate">${dateShort} • ${time} WIB</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-0.5">${court}</p>
                        <p class="text-xs font-bold text-[#0B7B69] mt-1 tabular-nums">${formatRupiah(price)}</p>
                    </div>
                    <button
                        type="button"
                        class="remove-slot-btn text-slate-400 hover:text-rose-500 p-1 rounded-lg hover:bg-slate-100 transition cursor-pointer"
                        data-remove-slot-id="${slotId}"
                        title="Hapus slot"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                `;
                slotsContainer.appendChild(summaryItem);
                recalculateSummary();

            } else if (currentStatus === 'selected') {
                // Change back to available
                card.setAttribute('data-status', 'available');
                card.classList.remove('border-2', 'border-[#0B7B69]', 'bg-emerald-50/50', 'shadow-xs', 'ring-2', 'ring-emerald-500/20');
                card.classList.add('bg-white', 'border-slate-200/90', 'hover:border-emerald-500/60', 'hover:shadow-xs');

                if (statusLabel) {
                    statusLabel.textContent = 'Available';
                    statusLabel.className = 'slot-status-label inline-flex items-center gap-1 text-xs font-bold text-[#10B981] bg-emerald-50 px-2 py-0.5 rounded-md';
                }

                // Remove from summary
                const existing = document.querySelector(`[data-summary-slot-id="${slotId}"]`);
                if (existing) existing.remove();
                recalculateSummary();
            }
        });
    });

    // Delegate remove button on summary item
    if (slotsContainer) {
        slotsContainer.addEventListener('click', function (e) {
            const removeBtn = e.target.closest('.remove-slot-btn');
            if (!removeBtn) return;

            const slotId = removeBtn.getAttribute('data-remove-slot-id');
            const summaryItem = document.querySelector(`[data-summary-slot-id="${slotId}"]`);
            if (summaryItem) summaryItem.remove();

            // Revert card state on grid
            const card = document.querySelector(`[data-slot-id="${slotId}"]`);
            if (card) {
                card.setAttribute('data-status', 'available');
                card.classList.remove('border-2', 'border-[#0B7B69]', 'bg-emerald-50/50', 'shadow-xs', 'ring-2', 'ring-emerald-500/20');
                card.classList.add('bg-white', 'border-slate-200/90', 'hover:border-emerald-500/60', 'hover:shadow-xs');

                const statusLabel = card.querySelector('.slot-status-label');
                if (statusLabel) {
                    statusLabel.textContent = 'Available';
                    statusLabel.className = 'slot-status-label inline-flex items-center gap-1 text-xs font-bold text-[#10B981] bg-emerald-50 px-2 py-0.5 rounded-md';
                }
            }

            recalculateSummary();
        });
    }

    // Inisialisasi awal summary dan link checkout
    recalculateSummary();
});
</script>
</x-layouts.penyewa>
