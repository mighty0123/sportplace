<div
    x-data="{
        toasts: [],
        addToast(toast) {
            const id = Date.now() + Math.random().toString(36).substring(2, 9);
            const type = (toast.type || 'success').toLowerCase();
            const message = toast.message || (typeof toast === 'string' ? toast : 'Operasi berhasil');
            
            this.toasts.push({ id, type, message });
            setTimeout(() => this.removeToast(id), 5000);
        },
        removeToast(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
        }
    }"
    x-init="
        @if (session('success'))
            addToast({ type: 'success', message: '{{ addslashes(session('success')) }}' });
        @endif
        @if (session('error'))
            addToast({ type: 'error', message: '{{ addslashes(session('error')) }}' });
        @endif
        @if (session('warning'))
            addToast({ type: 'warning', message: '{{ addslashes(session('warning')) }}' });
        @endif
        @if (session('status'))
            addToast({ type: 'info', message: '{{ addslashes(session('status')) }}' });
        @endif
    "
    @notify.window="addToast($event.detail)"
    class="fixed bottom-6 right-6 z-50 flex flex-col gap-2.5 max-w-sm w-full pointer-events-none px-4 sm:px-0"
    role="region"
    aria-label="Notifikasi Aplikasi"
>
    <template x-for="t in toasts" :key="t.id">
        <div
            x-show="true"
            x-transition:enter="transition duration-300 ease-out"
            x-transition:enter-start="opacity-0 translate-y-3 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition duration-200 ease-in"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-2 scale-95"
            class="pointer-events-auto rounded-2xl p-4 shadow-lg border flex items-center justify-between gap-3 text-xs sm:text-sm font-medium transition"
            :class="{
                'bg-emerald-50/95 border-emerald-200 text-emerald-950': t.type === 'success',
                'bg-rose-50/95 border-rose-200 text-rose-950': t.type === 'error',
                'bg-amber-50/95 border-amber-200 text-amber-950': t.type === 'warning',
                'bg-blue-50/95 border-blue-200 text-blue-950': t.type === 'info'
            }"
            role="alert"
        >
            <div class="flex items-center gap-3 min-w-0">
                <!-- Icon Success -->
                <template x-if="t.type === 'success'">
                    <div class="w-8 h-8 rounded-full bg-emerald-100 text-[#0B7B69] flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </template>

                <!-- Icon Error -->
                <template x-if="t.type === 'error'">
                    <div class="w-8 h-8 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                </template>

                <!-- Icon Warning -->
                <template x-if="t.type === 'warning'">
                    <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                </template>

                <!-- Icon Info -->
                <template x-if="t.type === 'info'">
                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </template>

                <p class="truncate leading-tight" x-text="t.message"></p>
            </div>

            <!-- Tombol Dismiss -->
            <button
                type="button"
                @click="removeToast(t.id)"
                class="p-1 rounded-lg hover:bg-black/5 opacity-70 hover:opacity-100 transition shrink-0 cursor-pointer"
                aria-label="Tutup Notifikasi"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </template>
</div>
