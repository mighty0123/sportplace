@props(['subtitle' => 'MitraHub', 'dark' => false])

<div class="flex items-center gap-3">
    <div class="w-10 h-10 rounded-xl bg-[#0B7B69] flex items-center justify-center text-white shadow-xs">
        <!-- Ikon Olahraga / Court SportPlace -->
        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <rect width="18" height="18" x="3" y="3" rx="4"/>
            <line x1="3" x2="21" y1="12" y2="12"/>
            <circle cx="12" cy="12" r="3"/>
        </svg>
    </div>
    <div class="flex flex-col text-left">
        <span class="text-xl font-bold tracking-tight {{ $dark ? 'text-white' : 'text-slate-900' }}">SportPlace</span>
        @if ($subtitle)
            <span class="text-xs font-semibold uppercase tracking-wider {{ $dark ? 'text-emerald-300' : 'text-[#0B7B69]' }}">{{ $subtitle }}</span>
        @endif
    </div>
</div>

