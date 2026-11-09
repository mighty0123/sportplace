@props([
    'image',
    'rating' => '4.8',
    'reviews' => '100',
    'sport' => 'Futsal',
    'city' => 'Denpasar',
    'venue' => null,
    'name',
    'facilities' => [],
    'price' => 100000,
    'href' => '#'
])

<div class="bg-white rounded-2xl border border-[#E1E3D9] overflow-hidden hover:border-[#88E7D4] hover:shadow-lg transition-all duration-300 flex flex-col group">
    <!-- Image & Badges Container -->
    <div class="relative aspect-[4/3] w-full overflow-hidden bg-slate-100">
        <img 
            src="{{ $image }}" 
            alt="{{ $name }}" 
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            loading="lazy"
        />

        <!-- Top Overlay Badges -->
        <div class="absolute inset-x-3 top-3 flex items-center justify-between pointer-events-none">
            <!-- Rating Badge -->
            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-black/60 backdrop-blur-md text-white text-xs font-semibold shadow-xs">
                <svg class="w-3.5 h-3.5 text-[#DEEF25] fill-current" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                <span class="tabular-nums">{{ $rating }}</span>
                <span class="text-white/70 text-[11px] font-normal">({{ $reviews }})</span>
            </div>

            <!-- Heart / Favorite Button -->
            <button 
                type="button" 
                class="pointer-events-auto w-8 h-8 rounded-full bg-black/40 hover:bg-black/70 backdrop-blur-md text-white flex items-center justify-center transition hover:scale-110 active:scale-95" 
                aria-label="Simpan ke favorit"
                onclick="event.preventDefault(); this.querySelector('svg').classList.toggle('fill-red-500'); this.querySelector('svg').classList.toggle('text-red-500');"
            >
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Content Details -->
    <div class="p-5 flex flex-col flex-grow justify-between">
        <div>
            <!-- Sport Category & Location -->
            <div class="text-xs font-medium text-[#7D8C78] mb-1.5 flex items-center gap-1.5 flex-wrap">
                <span>{{ $sport }}</span>
                <span>•</span>
                <span>{{ $city }}</span>
                @if(!empty($venue))
                    <span>•</span>
                    <span class="text-[#0A7660] font-semibold truncate">{{ $venue }}</span>
                @endif
            </div>

            <!-- Field Name -->
            <h3 class="font-bold text-base text-[#1B2724] group-hover:text-[#0A7660] transition-colors line-clamp-1 mb-3">
                <a href="{{ $href }}">{{ $name }}</a>
            </h3>

            <!-- Facilities Tags -->
            <div class="flex flex-wrap items-center gap-1.5 mb-5">
                @foreach($facilities as $facility)
                    <span class="text-[11px] font-medium text-[#0C4E40] bg-[#F1F9F7] border border-[#BBEDE3]/70 px-2.5 py-0.5 rounded-md">
                        {{ $facility }}
                    </span>
                @endforeach
            </div>
        </div>

        <!-- Price & Action Button Footer -->
        <div class="pt-4 border-t border-[#EFEFE6] flex items-center justify-between">
            <div class="flex flex-col">
                <div class="flex items-baseline gap-1">
                    <span class="text-base sm:text-lg font-bold text-[#1B2724] tabular-nums">
                        Rp {{ number_format($price, 0, ',', '.') }}
                    </span>
                    <span class="text-xs text-[#7D8C78]">/ jam</span>
                </div>
            </div>

            <a 
                href="{{ $href }}" 
                class="inline-flex items-center justify-center px-4 py-1.5 text-xs font-semibold text-[#0A7660] bg-[#DDF4EF] hover:bg-[#0A7660] hover:text-white rounded-full transition-all duration-200"
            >
                Lihat Detail
            </a>
        </div>
    </div>
</div>
