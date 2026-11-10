<div class="space-y-6 max-w-full min-w-0">

    <!-- Top Header & Breadcrumb -->
    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-xs">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-medium text-slate-500 mb-2">
                    <span class="text-slate-400">SportPlace Mitra</span>
                    <span class="text-slate-300">/</span>
                    <span class="text-slate-800 font-semibold">Notifikasi</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">
                    Pusat Notifikasi
                </h1>
                <p class="text-sm text-slate-500 mt-1 max-w-2xl leading-relaxed">
                    Pantau seluruh pemberitahuan penting terkait verifikasi kemitraan, moderasi venue/lapangan, dan reservasi masuk dari penyewa.
                </p>
            </div>

            <!-- Aksi Tandai Semua Dibaca (Reaktif) -->
            @if ($unreadTotal > 0)
                <div class="shrink-0 self-start md:self-auto">
                    <button
                        type="button"
                        wire:click="markAllAsRead"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-700 text-xs sm:text-sm font-semibold transition cursor-pointer min-h-[42px] disabled:opacity-50"
                    >
                        <span wire:loading.remove wire:target="markAllAsRead" class="inline-flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Tandai Semua Dibaca</span>
                        </span>
                        <span wire:loading wire:target="markAllAsRead" class="inline-flex items-center gap-2">
                            <svg class="animate-spin w-4 h-4 text-slate-600" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span>Memproses...</span>
                        </span>
                    </button>
                </div>
            @endif
        </div>

        <!-- Tab Filter: Semua vs Belum Dibaca (Reaktif) -->
        <div class="flex items-center gap-2 mt-6 pt-5 border-t border-slate-100">
            <button
                type="button"
                wire:click="setFilter('all')"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-semibold transition cursor-pointer {{ $filter === 'all' ? 'bg-[#0B7B69] text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
            >
                <span>Semua</span>
            </button>
            <button
                type="button"
                wire:click="setFilter('unread')"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-semibold transition cursor-pointer {{ $filter === 'unread' ? 'bg-[#0B7B69] text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
            >
                <span>Belum Dibaca</span>
                @if ($unreadTotal > 0)
                    <span class="inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold rounded-full {{ $filter === 'unread' ? 'bg-white text-[#0B7B69]' : 'bg-[#0B7B69] text-white' }}">
                        {{ $unreadTotal }}
                    </span>
                @endif
            </button>
        </div>
    </div>

    <!-- Daftar Notifikasi (Reaktif) -->
    @if ($notificationsList->isNotEmpty())
        <div class="space-y-3">
            @foreach ($notificationsList as $notification)
                @php
                    $data = $notification->data;
                    $type = $data['type'] ?? 'general';
                    $isUnread = $notification->unread();
                    $targetUrl = $data['url'] ?? '#';
                @endphp

                <div
                    wire:key="notif-{{ $notification->id }}"
                    class="bg-white rounded-2xl sm:rounded-3xl border transition p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 {{ $isUnread ? 'border-emerald-200 bg-emerald-50/15 shadow-xs' : 'border-slate-200/80 hover:border-slate-300' }}"
                >
                    <div class="flex items-start gap-3.5 sm:gap-4">
                        <!-- Icon Representasi Tipe Notifikasi -->
                        <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl flex items-center justify-center shrink-0 mt-0.5 {{ $isUnread ? 'bg-emerald-100 text-[#0B7B69]' : 'bg-slate-100 text-slate-500' }}">
                            @if ($type === 'mitra_verification')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            @elseif ($type === 'listing_moderation')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            @elseif ($type === 'new_booking')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            @endif
                        </div>

                        <div class="space-y-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h4 class="text-sm font-bold text-slate-900">
                                    {{ $data['title'] ?? 'Pemberitahuan Sistem' }}
                                </h4>
                                @if ($isUnread)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-[#0B7B69]">
                                        Baru
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed max-w-3xl">
                                {{ $data['message'] ?? '' }}
                            </p>
                            <span class="text-[11px] text-slate-400 block pt-0.5">
                                {{ $notification->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>

                    <!-- CTA Aksi Notifikasi (Reaktif) -->
                    <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
                        <button
                            type="button"
                            wire:click="markAsRead('{{ $notification->id }}')"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-full bg-[#0B7B69] hover:bg-[#086354] active:scale-95 text-white text-xs font-semibold shadow-xs transition cursor-pointer min-h-[36px]"
                        >
                            <span>{{ $targetUrl && $targetUrl !== '#' ? 'Lihat Detail' : 'Tandai Dibaca' }}</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination Livewire -->
        <div class="pt-2">
            {{ $notificationsList->links() }}
        </div>

    @else
        <!-- Empty State -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-12 text-center shadow-xs">
            <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
            </div>
            <h3 class="text-base font-bold text-slate-900 mb-1">
                {{ $filter === 'unread' ? 'Tidak Ada Notifikasi Baru' : 'Belum Ada Notifikasi' }}
            </h3>
            <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto leading-relaxed">
                {{ $filter === 'unread' ? 'Semua notifikasi Anda sudah dibaca. Notifikasi baru akan muncul otomatis di sini.' : 'Saat ada verifikasi, moderasi listing, atau pesanan baru, Anda akan menerima pemberitahuan di halaman ini.' }}
            </p>
            @if ($filter === 'unread')
                <div class="mt-4">
                    <button
                        type="button"
                        wire:click="setFilter('all')"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition cursor-pointer"
                    >
                        <span>Lihat Semua Notifikasi</span>
                    </button>
                </div>
            @endif
        </div>
    @endif

</div>
