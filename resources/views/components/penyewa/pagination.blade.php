@props([
    'from'        => 1,
    'to'          => 6,
    'total'       => 12,
    'currentPage' => 1,
    'lastPage'    => 2,
    'unit'        => 'pesanan',
])

<div class="px-5 sm:px-6 py-4 bg-white border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
    <!-- Info Counter -->
    <div class="text-xs text-slate-500 font-medium">
        Menampilkan <span class="font-bold text-slate-800">{{ $from }} - {{ $to }}</span> dari <span class="font-bold text-slate-800">{{ $total }}</span> {{ $unit }}
    </div>

    <!-- Page Controls -->
    <div class="flex items-center gap-1.5 sm:gap-2">
        <!-- Previous Button -->
        <button
            type="button"
            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-400 hover:text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:pointer-events-none transition"
            {{ $currentPage <= 1 ? 'disabled' : '' }}
        >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            <span>Sebelumnya</span>
        </button>

        <!-- Page Numbers -->
        @for ($p = 1; $p <= $lastPage; $p++)
            <button
                type="button"
                class="w-8 h-8 rounded-lg text-xs font-bold transition flex items-center justify-center {{ $p == $currentPage ? 'bg-[#0B7B69] text-white shadow-2xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}"
            >
                {{ $p }}
            </button>
        @endfor

        <!-- Next Button -->
        <button
            type="button"
            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-100 disabled:opacity-50 disabled:pointer-events-none transition"
            {{ $currentPage >= $lastPage ? 'disabled' : '' }}
        >
            <span>Selanjutnya</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    </div>
</div>
