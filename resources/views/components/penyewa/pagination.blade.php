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
        @if ($currentPage <= 1)
            <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-300 opacity-50 cursor-not-allowed select-none">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                <span>Sebelumnya</span>
            </span>
        @else
            <a
                href="{{ request()->fullUrlWithQuery(['page' => $currentPage - 1]) }}"
                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition"
            >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                <span>Sebelumnya</span>
            </a>
        @endif

        <!-- Page Numbers -->
        @for ($p = 1; $p <= $lastPage; $p++)
            @if ($p == $currentPage)
                <span class="w-8 h-8 rounded-lg text-xs font-bold transition flex items-center justify-center bg-[#0B7B69] text-white shadow-2xs select-none">
                    {{ $p }}
                </span>
            @else
                <a
                    href="{{ request()->fullUrlWithQuery(['page' => $p]) }}"
                    class="w-8 h-8 rounded-lg text-xs font-bold transition flex items-center justify-center text-slate-600 hover:bg-slate-100 hover:text-slate-900"
                >
                    {{ $p }}
                </a>
            @endif
        @endfor

        <!-- Next Button -->
        @if ($currentPage >= $lastPage)
            <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-300 opacity-50 cursor-not-allowed select-none">
                <span>Selanjutnya</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </span>
        @else
            <a
                href="{{ request()->fullUrlWithQuery(['page' => $currentPage + 1]) }}"
                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition"
            >
                <span>Selanjutnya</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        @endif
    </div>
</div>
