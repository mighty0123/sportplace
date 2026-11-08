@props([
    'name',
    'count' => 0,
    'href' => '#',
    'type' => 'futsal'
])

<a href="{{ $href }}" class="group flex flex-col items-center justify-between p-5 bg-white rounded-2xl border border-[#E1E3D9] hover:border-[#88E7D4] hover:shadow-md hover:-translate-y-1 transition-all duration-200">
    <!-- Icon Circle -->
    <div class="w-14 h-14 rounded-full bg-[#DDF4EF] group-hover:bg-[#BBEDE3] text-[#0A7660] flex items-center justify-center mb-3 transition-colors">
        @if($type === 'futsal')
            <!-- Futsal Soccer Ball Icon -->
            <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <polygon points="12 6 15 9 14 13 10 13 9 9 12 6" fill="currentColor" fill-opacity="0.15"/>
                <line x1="12" y1="6" x2="12" y2="2"/>
                <line x1="15" y1="9" x2="18.5" y2="7.5"/>
                <line x1="14" y1="13" x2="17.5" y2="16.5"/>
                <line x1="10" y1="13" x2="6.5" y2="16.5"/>
                <line x1="9" y1="9" x2="5.5" y2="7.5"/>
            </svg>
        @elseif($type === 'badminton')
            <!-- Badminton Shuttlecock Icon -->
            <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 19a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z"/>
                <path d="m10.5 15.5-4-9 11-2-1 11"/>
                <path d="m8 10 7-1.5"/>
                <path d="m9 13 5-1"/>
            </svg>
        @elseif($type === 'basket')
            <!-- Basketball Icon -->
            <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <path d="M2.5 12h19"/>
                <path d="M12 2.5a15 15 0 0 1 0 19"/>
                <path d="M12 2.5a15 15 0 0 0 0 19"/>
            </svg>
        @elseif($type === 'tenis')
            <!-- Tennis Ball Icon -->
            <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <path d="M5.5 3.5a10 10 0 0 1 0 17"/>
                <path d="M18.5 3.5a10 10 0 0 0 0 17"/>
            </svg>
        @elseif($type === 'voli')
            <!-- Volleyball Icon -->
            <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 12a10 10 0 0 1-5-8.66"/>
                <path d="M12 12a10 10 0 0 1 10 0"/>
                <path d="M12 12a10 10 0 0 1-5 8.66"/>
            </svg>
        @elseif($type === 'tenis-meja')
            <!-- Table Tennis Paddle Icon -->
            <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="10" r="7"/>
                <path d="m16 15 5 5"/>
                <circle cx="19" cy="6" r="2" fill="currentColor" fill-opacity="0.2"/>
            </svg>
        @else
            <!-- Default Sport Trophy -->
            <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/>
                <path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/>
                <path d="M4 22h16"/>
                <path d="M10 14.66V17c0 .55-.45 1-1 1H8c-.55 0-1 .45-1 1v1h10v-1c0-.55-.45-1-1-1h-1c-.55 0-1-.45-1-1v-2.34"/>
                <path d="M6 4h12v7a6 6 0 0 1-12 0V4Z"/>
            </svg>
        @endif
    </div>

    <!-- Category Name -->
    <h3 class="font-bold text-sm text-[#1B2724] mb-2 group-hover:text-[#0A7660] transition-colors">
        {{ $name }}
    </h3>

    <!-- Count Pill Badge -->
    <span class="inline-flex items-center text-[11px] font-medium text-[#0A7660] bg-[#EFFBF3] px-2.5 py-0.5 rounded-full border border-[#BAEECD]/60">
        {{ $count }} Lapangan
    </span>
</a>
