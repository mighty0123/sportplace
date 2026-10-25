<x-layouts.mitra :title="$title" :heading="$heading">
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-xs">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-6 border-b border-slate-100">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900">{{ $heading }}</h2>
                <p class="text-sm text-slate-500 mt-1">{{ $description }}</p>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 text-sm font-semibold transition cursor-pointer min-h-[44px]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span>Keluar Akun (Logout)</span>
                </button>
            </form>
        </div>

        <div class="py-12">
            <x-empty-state
                title="Halaman Segera Hadir"
                description="Modul ini akan diimplementasikan pada FASE 2 sesuai rancangan Figma."
                icon="⏳"
            />
        </div>
    </div>
</x-layouts.mitra>

