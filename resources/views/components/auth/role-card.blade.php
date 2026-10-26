@props([
    'value',
    'title',
    'subtitle',
    'features' => [],
    'icon' => 'user',
    'currentRole' => 'customer'
])

@php
    $isSelected = $currentRole === $value;
@endphp

<label 
    id="role-card-{{ $value }}"
    class="relative flex flex-col p-4 sm:p-5 rounded-2xl border-2 cursor-pointer transition-all duration-200 select-none {{ $isSelected ? 'bg-[#E8F7EE] border-[#0A7660] shadow-xs' : 'bg-[#F2F7F4] border-transparent hover:border-[#BBEDE3]' }}"
    onclick="handleRoleChange('{{ $value }}')"
>
    <!-- Hidden Radio Input -->
    <input 
        type="radio" 
        name="role" 
        value="{{ $value }}" 
        class="sr-only" 
        {{ $isSelected ? 'checked' : '' }}
    />

    <!-- Header: Icon, Title & Active Checkmark -->
    <div class="flex items-center justify-between mb-2">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-[#0A7660] text-white flex items-center justify-center shrink-0">
                @if($icon === 'venue')
                    <!-- Stadium/Venue Icon -->
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 21h18"/>
                        <path d="M5 21V7l7-4 7 4v14"/>
                        <path d="M9 10h6"/>
                        <path d="M9 14h6"/>
                        <path d="M9 18h6"/>
                    </svg>
                @else
                    <!-- User/Athlete Icon -->
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="7" r="4"/>
                        <path d="M5.5 21a8.38 8.38 0 0 1 13 0"/>
                    </svg>
                @endif
            </div>
            <h3 class="font-bold text-base text-[#1B2724]">
                {{ $title }}
            </h3>
        </div>

        <!-- Active Checkmark Indicator Badge -->
        <div id="check-badge-{{ $value }}" class="w-5 h-5 rounded-full bg-[#0A7660] text-white flex items-center justify-center transition-opacity {{ $isSelected ? 'opacity-100' : 'opacity-0' }}">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
    </div>

    <!-- Subtitle -->
    <p class="text-xs text-[#5A6C5B] mb-3.5 leading-snug">
        {{ $subtitle }}
    </p>

    <!-- Features Checkmark List -->
    <ul class="space-y-1.5 mt-auto text-[11px] text-[#0C4E40] font-medium">
        @foreach($features as $feature)
            <li class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-[#0A7660] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ $feature }}</span>
            </li>
        @endforeach
    </ul>
</label>
