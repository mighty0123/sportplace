<x-layouts.auth title="Verifikasi OTP — SportPlace">
    <div class="bg-white rounded-3xl border border-emerald-100 shadow-sm overflow-hidden flex flex-col lg:flex-row p-3 sm:p-4 lg:p-5 gap-6 lg:gap-8">

        <!-- ===== LEFT: Informational Banner ===== -->
        <div class="relative w-full lg:w-5/12 rounded-2xl overflow-hidden min-h-[300px] lg:min-h-full flex flex-col justify-between p-8 sm:p-10 bg-gradient-to-b from-[#06231D] via-[#0B322A] to-[#06231D]">
            <!-- Decorative glows -->
            <div class="absolute -top-16 -right-16 w-64 h-64 bg-[#0A7660]/30 blur-3xl pointer-events-none rounded-full"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-[#DEEF25]/10 blur-3xl pointer-events-none rounded-full"></div>

            <!-- Top: Logo & Step Indicator -->
            <div class="relative z-10 space-y-6">
                <!-- Step Progress -->
                <div class="flex items-center gap-2">
                    <!-- Step 1 (done) -->
                    <div class="flex items-center gap-1.5">
                        <div class="w-7 h-7 rounded-full bg-[#DEEF25] text-[#06231D] flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <span class="text-[10px] font-semibold text-emerald-100/60 uppercase tracking-wider hidden sm:block">Daftar</span>
                    </div>
                    <div class="flex-1 h-px bg-emerald-700/60 max-w-[24px]"></div>
                    <!-- Step 2 (current: OTP) -->
                    <div class="flex items-center gap-1.5">
                        <div class="w-7 h-7 rounded-full bg-[#0A7660] border-2 border-[#DEEF25] text-white flex items-center justify-center shrink-0 text-xs font-bold animate-pulse">
                            2
                        </div>
                        <span class="text-[10px] font-bold text-[#DEEF25] uppercase tracking-wider hidden sm:block">Verifikasi</span>
                    </div>
                    <div class="flex-1 h-px bg-emerald-700/60 max-w-[24px]"></div>
                    <!-- Step 3 (upcoming: Login) -->
                    <div class="flex items-center gap-1.5">
                        <div class="w-7 h-7 rounded-full bg-emerald-900/60 border border-emerald-700/60 text-emerald-500 flex items-center justify-center shrink-0 text-xs font-bold">
                            3
                        </div>
                        <span class="text-[10px] font-semibold text-emerald-100/40 uppercase tracking-wider hidden sm:block">Masuk</span>
                    </div>
                </div>

                <!-- Headline & Description -->
                <div class="space-y-3 pt-4">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#0C4E40]/80 border border-[#43E5C5]/20 text-[#DEEF25] text-xs font-bold tracking-wider">
                        <span class="flex h-2 w-2 rounded-full bg-[#DEEF25] animate-ping"></span>
                        Kode dikirim ke email Anda
                    </div>
                    <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-white leading-tight">
                        Satu langkah<br>
                        <span class="text-[#DEEF25]">lagi untuk masuk.</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-emerald-100/80 leading-relaxed">
                        Kode OTP 6 digit sudah dikirimkan ke alamat email Anda. Cek folder Inbox maupun Spam.
                    </p>
                </div>
            </div>

            <!-- Bottom: Security Tips -->
            <div class="relative z-10 pt-10 space-y-3">
                <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-400/80 mb-3">
                    Tips Keamanan
                </p>
                <div class="flex items-start gap-2.5">
                    <svg class="w-4 h-4 text-[#DEEF25] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <p class="text-xs text-emerald-100/70 leading-snug">
                        SportPlace <strong class="text-white">tidak pernah</strong> meminta kode OTP melalui telepon atau pesan dari pihak lain.
                    </p>
                </div>
                <div class="flex items-start gap-2.5">
                    <svg class="w-4 h-4 text-[#DEEF25] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-xs text-emerald-100/70 leading-snug">
                        Kode berlaku selama <strong class="text-white">10 menit</strong>. Segera masukkan sebelum kedaluwarsa.
                    </p>
                </div>
            </div>
        </div>

        <!-- ===== RIGHT: OTP Form Panel ===== -->
        <div class="w-full lg:w-7/12 p-4 sm:p-6 lg:p-10 flex flex-col justify-center">

            <!-- Form Header -->
            <div class="mb-8">
                <div class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#0A7660] mb-2">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <span>Verifikasi Akun — Langkah 2 dari 3</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-[#1B2724]">
                    Masukkan Kode OTP
                </h2>
                <p class="text-xs sm:text-sm text-[#5A6C5B] mt-1.5 leading-relaxed">
                    Untuk keamanan akun Anda, masukkan 6 digit kode verifikasi yang telah dikirim ke email:
                </p>
            </div>

            <!-- Email Destination Badge -->
            <div class="mb-7 flex items-center gap-3 p-4 rounded-2xl bg-[#F1F9F7] border border-[#BBEDE3]/70">
                <div class="w-9 h-9 rounded-xl bg-[#0A7660] text-white flex items-center justify-center shrink-0">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[11px] text-[#5A6C5B] font-medium">Kode dikirim ke</p>
                    <p class="text-sm font-bold text-[#1B2724] tabular-nums tracking-tight">{{ $email }}</p>
                </div>
            </div>

            <!-- Success / Status Alert -->
            @if (session('status'))
                <div class="mb-5 p-3.5 rounded-xl bg-[#E8F7EE] border border-[#BAEECD] flex items-start gap-2.5 text-xs font-medium text-[#0A7660]">
                    <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <!-- Error Alert -->
            @if ($errors->has('kode'))
                <div class="mb-5 p-3.5 rounded-xl bg-red-50 border border-red-200 flex items-start gap-2.5 text-xs font-medium text-red-700">
                    <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ $errors->first('kode') }}</span>
                </div>
            @endif

            <!-- OTP Verification Form -->
            <form method="POST" action="{{ route('otp.verify') }}" id="otp-form" class="space-y-6">
                @csrf

                <!-- 6-Digit OTP Input Boxes -->
                <div>
                    <label class="block text-xs font-semibold text-[#1B2724] mb-3">
                        Kode Verifikasi (6 Digit)
                    </label>

                    <!-- Hidden real input that gets submitted -->
                    <input type="hidden" name="kode" id="kode-hidden" autocomplete="one-time-code" />

                    <!-- Visual 6-box OTP input -->
                    <div class="flex items-center gap-2 sm:gap-3 justify-center" id="otp-boxes">
                        @for ($i = 0; $i < 6; $i++)
                            <input
                                type="text"
                                inputmode="numeric"
                                maxlength="1"
                                pattern="[0-9]"
                                class="otp-digit w-12 h-14 sm:w-14 sm:h-16 text-center text-2xl font-bold rounded-xl bg-[#F2F7F4] border-2 {{ $errors->has('kode') ? 'border-red-400 bg-red-50/30' : 'border-transparent' }} focus:bg-white focus:border-[#0A7660] focus:ring-2 focus:ring-[#0A7660]/20 text-[#1B2724] transition outline-none tabular-nums caret-transparent"
                                autocomplete="off"
                                aria-label="Digit {{ $i + 1 }} kode OTP"
                                id="otp-{{ $i }}"
                            />
                        @endfor
                    </div>

                    <p class="text-center text-[11px] text-[#7D8C78] mt-3">
                        Masukkan 6 digit angka yang tertera di email Anda
                    </p>
                </div>

                <!-- Submit CTA Button -->
                <button
                    type="submit"
                    id="btn-verify"
                    class="w-full flex items-center justify-center gap-2 py-3.5 px-6 rounded-xl bg-[#0A7660] hover:bg-[#0B604F] active:bg-[#0C4E40] text-white text-sm font-semibold transition shadow-sm disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <span>Verifikasi Akun</span>
                </button>

            </form>

            <!-- Resend Code Section -->
            <div class="mt-8 pt-6 border-t border-[#EFEFE6]">
                <div class="text-center space-y-3">
                    <p class="text-xs text-[#5A6C5B]">
                        Tidak menerima kode atau kode sudah kedaluwarsa?
                    </p>

                    <!-- Countdown Timer Display -->
                    <div id="resend-cooldown" class="flex items-center justify-center gap-1.5 text-xs text-[#7D8C78]">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Kirim ulang dalam <strong id="countdown-timer" class="tabular-nums font-bold text-[#1B2724]">60</strong> detik</span>
                    </div>

                    <form method="POST" action="{{ route('otp.resend') }}" id="resend-form">
                        @csrf
                        <button
                            type="submit"
                            id="btn-resend"
                            class="hidden inline-flex items-center gap-1.5 text-xs font-bold text-[#0A7660] hover:text-[#0B604F] hover:underline transition disabled:opacity-40"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <span>Kirim Ulang Kode OTP</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Bottom Link -->
            <div class="mt-4 text-center text-xs text-[#5A6C5B]">
                Salah akun?
                <a href="{{ route('register') }}" class="font-bold text-[#0A7660] hover:text-[#0B604F] hover:underline transition ml-1">
                    Daftar dengan akun lain
                </a>
            </div>

        </div>
    </div>

    <!-- OTP Box Auto-Focus & Submit Logic -->
    <script>
        (function () {
            const boxes = document.querySelectorAll('.otp-digit');
            const hiddenInput = document.getElementById('kode-hidden');
            const form = document.getElementById('otp-form');

            function getCode() {
                return Array.from(boxes).map(b => b.value).join('');
            }

            function syncHidden() {
                hiddenInput.value = getCode();
            }

            boxes.forEach((box, idx) => {
                box.addEventListener('input', function (e) {
                    const val = this.value.replace(/\D/g, '');
                    this.value = val ? val[0] : '';
                    syncHidden();
                    // Auto-advance to next
                    if (val && idx < boxes.length - 1) {
                        boxes[idx + 1].focus();
                    }
                });

                box.addEventListener('keydown', function (e) {
                    if (e.key === 'Backspace' && !this.value && idx > 0) {
                        boxes[idx - 1].focus();
                        boxes[idx - 1].value = '';
                        syncHidden();
                    }
                    // Allow paste
                    if (e.key === 'ArrowLeft' && idx > 0) boxes[idx - 1].focus();
                    if (e.key === 'ArrowRight' && idx < boxes.length - 1) boxes[idx + 1].focus();
                });

                box.addEventListener('paste', function (e) {
                    e.preventDefault();
                    const pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 6);
                    pasted.split('').forEach((char, i) => {
                        if (boxes[idx + i]) boxes[idx + i].value = char;
                    });
                    syncHidden();
                    const lastFilled = Math.min(idx + pasted.length - 1, boxes.length - 1);
                    boxes[lastFilled].focus();
                });
            });

            // Auto submit when all 6 digits filled
            form.addEventListener('input', function () {
                const code = getCode();
                if (code.length === 6) {
                    setTimeout(() => form.requestSubmit(), 200);
                }
            });

            // Countdown timer for resend button
            let seconds = 60;
            const timerEl = document.getElementById('countdown-timer');
            const cooldownEl = document.getElementById('resend-cooldown');
            const resendBtn = document.getElementById('btn-resend');

            const interval = setInterval(() => {
                seconds--;
                timerEl.textContent = seconds;
                if (seconds <= 0) {
                    clearInterval(interval);
                    cooldownEl.classList.add('hidden');
                    resendBtn.classList.remove('hidden');
                }
            }, 1000);

            // Focus first empty box on load
            boxes[0].focus();
        })();
    </script>
</x-layouts.auth>
