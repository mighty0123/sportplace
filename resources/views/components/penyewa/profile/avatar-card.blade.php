@props([
    'name'        => 'Dimas Pratama',
    'memberSince' => 'April 2023',
    'status'      => 'Penyewa Aktif',
    'email'       => 'dimas.pratama@email.com',
    'phone'       => '+62 812-3456-7890',
    'avatar'      => null,
    'initials'    => null,
])

@php
    $userInitials = $initials ?? collect(explode(' ', trim($name)))->take(2)->map(fn ($w) => mb_substr($w, 0, 1))->implode('');
@endphp

<div class="bg-white rounded-2xl border border-slate-200/90 p-6 sm:p-7 shadow-xs flex flex-col items-center text-center">
    <!-- User Avatar Photo / Initials Fallback with Camera Trigger -->
    <div class="relative mb-4 group">
        <div class="relative w-28 h-28 sm:w-32 sm:h-32 rounded-full overflow-hidden ring-4 ring-emerald-500/20 shadow-md bg-[#0B7B69] shrink-0 flex items-center justify-center text-white text-3xl font-extrabold select-none">
            @if ($avatar)
                <img
                    src="{{ $avatar }}"
                    alt="{{ $name }}"
                    class="w-full h-full object-cover"
                    loading="lazy"
                    onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');"
                >
                <span class="hidden">{{ $userInitials }}</span>
            @else
                <span>{{ $userInitials }}</span>
            @endif
        </div>

        <!-- Camera badge trigger for uploading/changing photo -->
        <button
            type="button"
            onclick="document.getElementById('edit-profile-modal')?.classList.remove('hidden'); document.getElementById('foto_profil')?.focus();"
            class="absolute bottom-1 right-1 w-9 h-9 rounded-full bg-[#0B7B69] hover:bg-[#086354] active:scale-95 text-white flex items-center justify-center shadow-md ring-2 ring-white transition cursor-pointer"
            title="Ubah Foto Profil"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </button>
    </div>

    <!-- Name & Membership -->
    <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 leading-tight">
        {{ $name }}
    </h2>
    <p class="text-xs text-slate-400 mt-1">
        Member sejak {{ $memberSince }}
    </p>

    <!-- Status Badge -->
    <div class="mt-3">
        <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/80 shadow-2xs">
            <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
            <span>{{ $status }}</span>
        </span>
    </div>

    <!-- Contact Info Container -->
    <div class="w-full mt-6 p-4 rounded-xl bg-slate-50 border border-slate-100 text-left space-y-2.5">
        <div class="flex items-center gap-2.5 text-xs text-slate-600 truncate">
            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            <span class="truncate" title="{{ $email }}">{{ $email }}</span>
        </div>
        <div class="flex items-center gap-2.5 text-xs text-slate-600">
            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
            </svg>
            <span>{{ $phone }}</span>
        </div>
    </div>

    <!-- Edit Profil Action Trigger -->
    <div class="mt-5 w-full">
        <button
            type="button"
            onclick="document.getElementById('edit-profile-modal').classList.remove('hidden')"
            class="inline-flex items-center justify-center gap-1.5 text-xs font-bold text-[#0B7B69] hover:text-[#086354] hover:underline transition py-1 cursor-pointer"
        >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
            </svg>
            <span>Edit Profil</span>
        </button>
    </div>
</div>
