@props([
    'title',
    'description',
    'icon' => 'choice'
])

<div class="bg-white rounded-2xl border border-[#E1E3D9] p-6 hover:border-[#88E7D4] hover:shadow-md transition-all duration-300 flex flex-col">
    <!-- Icon Container -->
    <div class="w-12 h-12 rounded-xl bg-[#EFFBF3] border border-[#BAEECD]/50 text-[#0A7660] flex items-center justify-center mb-5 shrink-0">
        @if($icon === 'choice')
            <!-- Many Choices / Grid -->
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect width="7" height="7" x="3" y="3" rx="1"/>
                <rect width="7" height="7" x="14" y="3" rx="1"/>
                <rect width="7" height="7" x="14" y="14" rx="1"/>
                <rect width="7" height="7" x="3" y="14" rx="1"/>
            </svg>
        @elseif($icon === 'price')
            <!-- Transparent Price / Tag -->
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 2H2v10l9.29 9.29c.94.94 2.48.94 3.42 0l6.58-6.58c.94-.94.94-2.48 0-3.42L12 2Z"/>
                <path d="M7 7h.01"/>
            </svg>
        @elseif($icon === 'calendar')
            <!-- Realtime Schedule / Calendar -->
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                <line x1="16" x2="16" y1="2" y2="6"/>
                <line x1="8" x2="8" y1="2" y2="6"/>
                <line x1="3" x2="21" y1="10" y2="10"/>
                <path d="m9 16 2 2 4-4"/>
            </svg>
        @elseif($icon === 'bolt')
            <!-- Practical Fast Booking / Lightning -->
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M13 2 3 14h9l-1 8 10-12h-9l1-8z"/>
            </svg>
        @else
            <!-- Fallback Star -->
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
            </svg>
        @endif
    </div>

    <!-- Title -->
    <h3 class="font-bold text-base sm:text-lg text-[#1B2724] mb-2">
        {{ $title }}
    </h3>

    <!-- Description -->
    <p class="text-xs sm:text-sm text-[#5A6C5B] leading-relaxed">
        {{ $description }}
    </p>
</div>
