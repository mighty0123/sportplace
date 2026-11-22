<div>
    @if ($variant === 'sidebar')
        @php
            $isActive = request()->routeIs('mitra.notifications.*');
        @endphp
        <a
            href="{{ route('mitra.notifications.index') }}"
            class="flex items-center justify-between px-4 py-2.5 rounded-full text-sm font-medium transition {{ $isActive ? 'bg-[#0B7B69] text-white shadow-xs' : 'text-emerald-100/70 hover:bg-[#07261F] hover:text-white' }}"
        >
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <span>Notifikasi</span>
            </div>
            @if ($this->unreadCount > 0)
                <span class="inline-flex items-center justify-center px-2 py-0.5 text-[11px] font-bold rounded-full bg-[#DEEF25] text-slate-900 tabular-nums">
                    {{ $this->unreadCount }}
                </span>
            @endif
        </a>
    @else
        <!-- Header Notification Bell with Interactive Dropdown Preview -->
        <div x-data="{ open: false }" @click.outside="open = false" class="relative">
            <button
                type="button"
                @click="open = !open"
                class="relative p-2 rounded-full text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition min-h-[44px] min-w-[44px] flex items-center justify-center cursor-pointer focus:outline-none"
                aria-label="Pusat Notifikasi"
                title="Pusat Notifikasi"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                @if ($this->unreadCount > 0)
                    <span class="absolute top-1.5 right-1.5 flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#0B7B69] opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-[#0B7B69] ring-2 ring-white"></span>
                    </span>
                @endif
            </button>

            <!-- Dropdown Preview Panel -->
            <div
                x-show="open"
                x-cloak
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-3xl shadow-xl border border-slate-200/90 z-50 overflow-hidden"
            >
                <!-- Dropdown Header -->
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between gap-3 bg-slate-50/70">
                    <div class="flex items-center gap-2">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-800">Notifikasi</h4>
                        @if ($this->unreadCount > 0)
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-100 text-[#0B7B69]">
                                {{ $this->unreadCount }} Baru
                            </span>
                        @endif
                    </div>
                    @if ($this->unreadCount > 0)
                        <button
                            type="button"
                            wire:click="markAllAsRead"
                            class="text-[11px] font-semibold text-[#0B7B69] hover:text-[#086354] hover:underline cursor-pointer"
                        >
                            Tandai semua dibaca
                        </button>
                    @endif
                </div>

                <!-- Dropdown List -->
                <div class="max-h-80 overflow-y-auto divide-y divide-slate-100">
                    @forelse ($this->recentNotifications as $item)
                        @php
                            $data = $item->data;
                            $type = $data['type'] ?? 'general';
                            $isUnread = $item->unread();
                        @endphp
                        <button
                            type="button"
                            wire:click="markAsRead('{{ $item->id }}')"
                            class="w-full text-left p-4 hover:bg-slate-50/80 transition flex items-start gap-3 cursor-pointer {{ $isUnread ? 'bg-emerald-50/20' : '' }}"
                        >
                            <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 mt-0.5 {{ $isUnread ? 'bg-emerald-100 text-[#0B7B69]' : 'bg-slate-100 text-slate-400' }}">
                                @if ($type === 'mitra_verification')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                @elseif ($type === 'listing_moderation')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                @elseif ($type === 'new_booking')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="text-xs font-bold text-slate-800 truncate">
                                        {{ $data['title'] ?? 'Pemberitahuan Sistem' }}
                                    </p>
                                    @if ($isUnread)
                                        <span class="w-2 h-2 rounded-full bg-[#0B7B69] shrink-0"></span>
                                    @endif
                                </div>
                                <p class="text-[11px] text-slate-500 line-clamp-2 mt-0.5 leading-snug">
                                    {{ $data['message'] ?? '' }}
                                </p>
                                <span class="text-[10px] text-slate-400 mt-1 block">
                                    {{ $item->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </button>
                    @empty
                        <div class="p-8 text-center">
                            <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            </div>
                            <p class="text-xs font-medium text-slate-500">Belum ada notifikasi</p>
                        </div>
                    @endforelse
                </div>

                <!-- Dropdown Footer -->
                <div class="p-3 bg-slate-50 border-t border-slate-100 text-center">
                    <a
                        href="{{ route('mitra.notifications.index') }}"
                        @click="open = false"
                        class="inline-flex items-center justify-center gap-1.5 text-xs font-bold text-[#0B7B69] hover:text-[#086354] transition py-1"
                    >
                        <span>Lihat Semua Notifikasi</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
