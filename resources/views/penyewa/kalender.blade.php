{{--
    Halaman Kalender & Jadwal Booking Lapangan — SportPlace
    Layout  : x-layouts.penyewa (Top navbar-based, no sidebar)
    Design  : Sesuai referensi Figma (Venue Banner, Date Carousel, Status Legend, Slot Matrix, Sticky Booking Summary)
--}}
<x-layouts.penyewa title="Jadwal Lapangan — SportPlace" active="kalender">
<div class="space-y-6">

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
         SECTION 2: Date Selector Carousel
    ========================================================= --}}
    <x-penyewa.calendar.date-strip
        :dates="$dates"
        :selected-date="$selectedDate"
    />

    {{-- =========================================================
         SECTION 3: Status Legend Indicators
    ========================================================= --}}
    <x-penyewa.calendar.legend />

    {{-- =========================================================
         SECTION 4: 2-Column Schedule Timetable & Sticky Summary
    ========================================================= --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        {{-- ---- Left 8 Cols: Slot Matrix Timetable ---- --}}
        <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[580px]">
                    <!-- Table Header: Courts -->
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/50">
                            <!-- Waktu Column Header -->
                            <th scope="col" class="py-4 px-4 sm:px-6 w-32 sm:w-36 text-center text-xs font-bold text-slate-400 uppercase tracking-wider">
                                WAKTU
                            </th>

                            <!-- Dynamic Court Headers -->
                            @foreach ($courts as $court)
                                <th scope="col" class="py-3 px-2 sm:px-3 text-center">
                                    <div class="py-2.5 px-3 rounded-xl bg-slate-100/90 border border-slate-200/80">
                                        <span class="block text-xs font-bold text-slate-900 leading-tight">
                                            {{ $court['name'] }}
                                        </span>
                                        <span class="block text-[11px] font-medium text-slate-500 mt-0.5">
                                            {{ $court['surface'] }}
                                        </span>
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>

                    <!-- Table Body: Timetable Rows -->
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($timeSlots as $row)
                            <tr class="hover:bg-slate-50/40 transition-colors">
                                <!-- Time Label Column -->
                                <td class="py-3.5 px-4 sm:px-6 text-xs font-semibold text-slate-600 text-center whitespace-nowrap">
                                    {{ $row['time'] }}
                                </td>

                                <!-- Court Columns -->
                                @foreach ($courts as $court)
                                    @php
                                        $slotData = $row['slots'][$court['id']] ?? [
                                            'status' => 'available',
                                            'label' => 'Tersedia',
                                            'price' => 150000,
                                            'court' => $court['name'],
                                        ];
                                    @endphp
                                    <td class="py-2.5 px-2 sm:px-3">
                                        <x-penyewa.calendar.slot-cell
                                            :item="$slotData"
                                            :time="$row['time']"
                                        />
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ---- Right 4 Cols: Booking Summary (Sticky) ---- --}}
        <div class="lg:col-span-4">
            <x-penyewa.calendar.booking-summary
                :selected-date="$selectedDate"
                :selected-slots="$selectedSlots"
                :service-fee="$serviceFee"
                :rental-total="$rentalTotal"
                :grand-total="$grandTotal"
                checkout-url="{{ url('/penyewa/pembayaran') }}"
            />
        </div>

    </div>

</div>

{{-- Interactive Slot Toggle Script --}}
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

        let totalRental = 0;
        items.forEach(function (item) {
            totalRental += parseInt(item.getAttribute('data-price') || 0, 10);
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
    }

    // Toggle Slot Button Click
    document.querySelectorAll('[data-slot-cell]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const currentStatus = btn.getAttribute('data-status');
            const slotId = btn.getAttribute('data-slot-id');
            const price = parseInt(btn.getAttribute('data-price') || 0, 10);
            const court = btn.getAttribute('data-court') || 'Court';
            const time = btn.getAttribute('data-time') || '';

            if (currentStatus === 'available') {
                // Change to selected
                btn.setAttribute('data-status', 'selected');
                btn.className = 'slot-btn w-full py-2.5 px-2 rounded-xl text-center bg-[#0B7B69] hover:bg-[#086354] border border-[#0B7B69] text-white transition duration-150 shadow-xs focus:outline-none focus:ring-2 focus:ring-emerald-400/40';
                btn.innerHTML = `
                    <span class="flex items-center justify-center gap-1 text-xs font-bold text-white leading-tight">
                        <svg class="w-3.5 h-3.5 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Dipilih Anda</span>
                    </span>
                    <span class="block text-[11px] font-medium text-emerald-100 mt-0.5 tabular-nums">
                        ${formatRupiah(price)}
                    </span>
                `;

                // Add to summary
                const summaryItem = document.createElement('div');
                summaryItem.className = 'slot-summary-item bg-slate-50 border border-slate-100 rounded-xl p-3 flex items-start justify-between gap-3 transition hover:border-slate-200';
                summaryItem.setAttribute('data-summary-slot-id', slotId);
                summaryItem.setAttribute('data-price', price);
                summaryItem.innerHTML = `
                    <div class="min-w-0">
                        <div class="flex items-center gap-1.5 text-xs font-bold text-slate-800">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                            <span class="truncate">${court}</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">${time} WIB</p>
                        <p class="text-xs font-bold text-[#0B7B69] mt-1 tabular-nums">${formatRupiah(price)}</p>
                    </div>
                    <button
                        type="button"
                        class="remove-slot-btn text-slate-400 hover:text-rose-500 p-1 rounded-lg hover:bg-slate-100 transition"
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
                btn.setAttribute('data-status', 'available');
                btn.className = 'slot-btn w-full py-2.5 px-2 rounded-xl text-center bg-emerald-50/60 hover:bg-emerald-100/80 border border-emerald-200/60 text-emerald-800 transition duration-150 shadow-2xs group focus:outline-none focus:ring-2 focus:ring-emerald-500/30';
                btn.innerHTML = `
                    <span class="block text-xs font-bold text-emerald-700 group-hover:text-emerald-800 leading-tight">
                        Tersedia
                    </span>
                    <span class="block text-[11px] font-medium text-emerald-600/90 mt-0.5 tabular-nums">
                        ${formatRupiah(price)}
                    </span>
                `;

                // Remove from summary
                const existing = document.querySelector(`[data-summary-slot-id="${slotId}"]`);
                if (existing) existing.remove();
                recalculateSummary();
            }
        });
    });

    // Delegate remove button click on summary card
    if (slotsContainer) {
        slotsContainer.addEventListener('click', function (e) {
            const removeBtn = e.target.closest('.remove-slot-btn');
            if (!removeBtn) return;

            const slotId = removeBtn.getAttribute('data-remove-slot-id');
            const summaryItem = document.querySelector(`[data-summary-slot-id="${slotId}"]`);
            if (summaryItem) summaryItem.remove();

            // Reset slot cell in table if present
            const slotBtn = document.querySelector(`[data-slot-id="${slotId}"]`);
            if (slotBtn) {
                const price = parseInt(slotBtn.getAttribute('data-price') || 0, 10);
                slotBtn.setAttribute('data-status', 'available');
                slotBtn.className = 'slot-btn w-full py-2.5 px-2 rounded-xl text-center bg-emerald-50/60 hover:bg-emerald-100/80 border border-emerald-200/60 text-emerald-800 transition duration-150 shadow-2xs group focus:outline-none focus:ring-2 focus:ring-emerald-500/30';
                slotBtn.innerHTML = `
                    <span class="block text-xs font-bold text-emerald-700 group-hover:text-emerald-800 leading-tight">
                        Tersedia
                    </span>
                    <span class="block text-[11px] font-medium text-emerald-600/90 mt-0.5 tabular-nums">
                        ${formatRupiah(price)}
                    </span>
                `;
            }

            recalculateSummary();
        });
    }
});
</script>
</x-layouts.penyewa>
