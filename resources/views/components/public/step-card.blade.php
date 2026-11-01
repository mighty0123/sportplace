@props([
    'number' => '01',
    'title',
    'description',
    'isLast' => false,
    'highlight' => false
])

<div class="relative flex flex-col items-center text-center group">
    <!-- Step Circle Number -->
    <div class="relative z-10 w-14 h-14 rounded-full flex items-center justify-center font-bold text-sm mb-4 transition-transform group-hover:scale-110 shadow-xs {{ $highlight ? 'bg-[#0A7660] text-white border-2 border-[#DEEF25]' : 'bg-white text-[#0A7660] border-2 border-[#0A7660]' }}">
        <span class="tabular-nums">{{ $number }}</span>
    </div>

    <!-- Title -->
    <h3 class="font-bold text-base text-[#1B2724] mb-1.5 group-hover:text-[#0A7660] transition-colors">
        {{ $title }}
    </h3>

    <!-- Description -->
    <p class="text-xs sm:text-sm text-[#5A6C5B] leading-relaxed max-w-[220px]">
        {{ $description }}
    </p>
</div>
