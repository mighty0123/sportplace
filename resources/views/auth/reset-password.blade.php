<x-layouts.auth title="Reset Password — SportPlace">
    <div class="bg-white rounded-3xl border border-emerald-100 shadow-sm overflow-hidden flex flex-col lg:flex-row p-3 sm:p-4 lg:p-5 gap-6 lg:gap-8">

        <!-- ===== LEFT: Banner ===== -->
        <div class="relative w-full lg:w-5/12 rounded-2xl overflow-hidden min-h-[280px] lg:min-h-full flex flex-col justify-between p-8 sm:p-10 bg-gradient-to-b from-[#06231D] via-[#0B322A] to-[#06231D]">
            <!-- Decorative glows -->
            <div class="absolute -top-16 -right-16 w-64 h-64 bg-[#0A7660]/30 blur-3xl pointer-events-none rounded-full"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-[#DEEF25]/10 blur-3xl pointer-events-none rounded-full"></div>

            <!-- Icon + Headline -->
            <div class="relative z-10 space-y-6">
                <!-- Shield Check Icon -->
                <div class="w-16 h-16 rounded-2xl bg-[#0A7660]/40 border border-[#43E5C5]/20 flex items-center justify-center">
                    <svg class="w-8 h-8 text-[#DEEF25]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>

                <div class="space-y-3">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#0C4E40]/80 border border-[#43E5C5]/20 text-[#DEEF25] text-xs font-bold tracking-wider">
                        PEMBARUAN SANDI
                    </div>
                    <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-white leading-tight">
                        Buat Kata<br>
                        <span class="text-[#DEEF25]">Sandi Baru.</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-emerald-100/80 leading-relaxed">
                        Anda hampir selesai. Buat kata sandi yang kuat dan mudah Anda ingat namun sulit ditebak orang lain.
                    </p>
                </div>
            </div>

            <!-- Bottom: Password Tips -->
            <div class="relative z-10 pt-10 space-y-3">
                <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-400/80 mb-3">Tips Kata Sandi Kuat</p>

                <div class="flex items-start gap-2.5">
                    <svg class="w-4 h-4 text-[#DEEF25] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <p class="text-xs text-emerald-100/70 leading-snug">Gunakan minimal <strong class="text-white">8 karakter</strong> campuran huruf dan angka</p>
                </div>
                <div class="flex items-start gap-2.5">
                    <svg class="w-4 h-4 text-[#DEEF25] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <p class="text-xs text-emerald-100/70 leading-snug">Tambahkan <strong class="text-white">karakter khusus</strong> seperti @, #, !, $ untuk keamanan ekstra</p>
                </div>
                <div class="flex items-start gap-2.5">
                    <svg class="w-4 h-4 text-[#DEEF25] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <p class="text-xs text-emerald-100/70 leading-snug">Jangan gunakan kata sandi yang <strong class="text-white">sama</strong> dengan akun lain</p>
                </div>
            </div>
        </div>

        <!-- ===== RIGHT: Form Panel ===== -->
        <div class="w-full lg:w-7/12 p-4 sm:p-6 lg:p-10 flex flex-col justify-center">

            <!-- Form Header -->
            <div class="mb-6">
                <div class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#0A7660] mb-2">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <span>Setel Ulang Kata Sandi</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-[#1B2724]">
                    Buat Sandi Baru
                </h2>
                <p class="text-xs sm:text-sm text-[#5A6C5B] mt-1.5 leading-relaxed">
                    Pastikan kata sandi baru Anda minimal 8 karakter dan tidak mudah ditebak.
                </p>
            </div>

            <!-- Email Badge (account indicator) -->
            <div class="mb-6 flex items-center gap-3 p-4 rounded-2xl bg-[#F1F9F7] border border-[#BBEDE3]/70">
                <div class="w-9 h-9 rounded-xl bg-[#0A7660] text-white flex items-center justify-center shrink-0">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[11px] text-[#5A6C5B] font-medium">Mereset kata sandi untuk</p>
                    <p class="text-sm font-bold text-[#1B2724] tabular-nums tracking-tight">{{ $email }}</p>
                </div>
            </div>

            <!-- Reset Password Form -->
            <form method="POST" action="{{ route('password.update') }}" id="reset-form" class="space-y-5">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                <!-- New Password -->
                <div class="space-y-1.5">
                    <label for="password" class="block text-xs font-semibold text-[#1B2724]">
                        Kata Sandi Baru <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-[#7D8C78]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input
                            type="password"
                            name="password"
                            id="password"
                            placeholder="Minimal 8 karakter"
                            minlength="8"
                            required
                            autofocus
                            class="w-full pl-10 pr-11 py-3 text-sm rounded-xl border-2 {{ $errors->has('password') ? 'border-rose-400 bg-rose-50/20 text-rose-900 focus:ring-rose-500/20 focus:border-rose-400' : 'border-[#D7E9DF] bg-[#F7FBF9] text-[#1B2724] focus:border-[#0A7660] focus:ring-[#0A7660]/10' }} focus:outline-none focus:ring-4 transition placeholder-[#9DB0A8]"
                        />
                        <button type="button" id="toggle-password" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-[#7D8C78] hover:text-[#0A7660] transition">
                            <svg id="eye-show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg id="eye-hide" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-xs text-rose-600 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror

                    <!-- Password Strength Bar -->
                    <div class="space-y-1.5 pt-1">
                        <div class="flex gap-1">
                            <div class="h-1.5 flex-1 rounded-full bg-[#EAF1EC] overflow-hidden">
                                <div id="strength-bar-1" class="h-full w-0 rounded-full transition-all duration-300"></div>
                            </div>
                            <div class="h-1.5 flex-1 rounded-full bg-[#EAF1EC] overflow-hidden">
                                <div id="strength-bar-2" class="h-full w-0 rounded-full transition-all duration-300"></div>
                            </div>
                            <div class="h-1.5 flex-1 rounded-full bg-[#EAF1EC] overflow-hidden">
                                <div id="strength-bar-3" class="h-full w-0 rounded-full transition-all duration-300"></div>
                            </div>
                            <div class="h-1.5 flex-1 rounded-full bg-[#EAF1EC] overflow-hidden">
                                <div id="strength-bar-4" class="h-full w-0 rounded-full transition-all duration-300"></div>
                            </div>
                        </div>
                        <p id="strength-label" class="text-[11px] text-[#9DB0A8]">Masukkan kata sandi untuk melihat kekuatannya</p>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="space-y-1.5">
                    <label for="password_confirmation" class="block text-xs font-semibold text-[#1B2724]">
                        Ulangi Kata Sandi Baru <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-[#7D8C78]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <input
                            type="password"
                            name="password_confirmation"
                            id="password_confirmation"
                            placeholder="Ketik ulang kata sandi baru"
                            minlength="8"
                            required
                            class="w-full pl-10 pr-11 py-3 text-sm rounded-xl border-2 {{ $errors->has('password_confirmation') ? 'border-rose-400 bg-rose-50/20 text-rose-900 focus:ring-rose-500/20 focus:border-rose-400' : 'border-[#D7E9DF] bg-[#F7FBF9] text-[#1B2724] focus:border-[#0A7660] focus:ring-[#0A7660]/10' }} focus:outline-none focus:ring-4 transition placeholder-[#9DB0A8]"
                        />
                        <button type="button" id="toggle-confirm" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-[#7D8C78] hover:text-[#0A7660] transition">
                            <svg id="eye-show-c" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg id="eye-hide-c" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                    <!-- Match Indicator -->
                    <p id="match-indicator" class="text-[11px] hidden"></p>
                    @error('password_confirmation')
                        <p class="text-xs text-rose-600 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    id="btn-update"
                    class="w-full flex items-center justify-center gap-2 py-3.5 px-6 rounded-xl bg-[#0A7660] hover:bg-[#0B604F] active:bg-[#0C4E40] text-white text-sm font-semibold transition shadow-sm"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Perbarui Kata Sandi</span>
                </button>
            </form>

            <!-- Back to Login -->
            <div class="mt-6 text-center">
                <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#5A6C5B] hover:text-[#0A7660] hover:underline transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke halaman Masuk
                </a>
            </div>
        </div>
    </div>

    <!-- Password toggle & strength meter JS -->
    <script>
        (function () {
            // Toggle password visibility
            function toggleVisibility(inputId, showIconId, hideIconId) {
                const input = document.getElementById(inputId);
                const showIcon = document.getElementById(showIconId);
                const hideIcon = document.getElementById(hideIconId);
                const isHidden = input.type === 'password';
                input.type = isHidden ? 'text' : 'password';
                showIcon.classList.toggle('hidden', isHidden);
                hideIcon.classList.toggle('hidden', !isHidden);
            }

            document.getElementById('toggle-password')?.addEventListener('click', () => {
                toggleVisibility('password', 'eye-show', 'eye-hide');
            });
            document.getElementById('toggle-confirm')?.addEventListener('click', () => {
                toggleVisibility('password_confirmation', 'eye-show-c', 'eye-hide-c');
            });

            // Strength meter
            const passwordInput = document.getElementById('password');
            const bars = [1, 2, 3, 4].map(n => document.getElementById('strength-bar-' + n));
            const label = document.getElementById('strength-label');

            const levels = [
                { color: '#ef4444', label: 'Sangat Lemah', min: 1 },
                { color: '#f97316', label: 'Lemah', min: 2 },
                { color: '#eab308', label: 'Cukup', min: 3 },
                { color: '#0A7660', label: 'Kuat 💪', min: 4 },
            ];

            function getStrength(val) {
                let score = 0;
                if (val.length >= 8) score++;
                if (/[A-Z]/.test(val)) score++;
                if (/[0-9]/.test(val)) score++;
                if (/[^A-Za-z0-9]/.test(val)) score++;
                return score;
            }

            passwordInput?.addEventListener('input', function () {
                const score = getStrength(this.value);
                const level = levels[score - 1] || null;
                bars.forEach((bar, i) => {
                    if (level && i < score) {
                        bar.style.width = '100%';
                        bar.style.backgroundColor = level.color;
                    } else {
                        bar.style.width = '0%';
                    }
                });
                label.textContent = this.value ? (level ? level.label : 'Sangat Lemah') : 'Masukkan kata sandi untuk melihat kekuatannya';
                label.style.color = level ? level.color : '#9DB0A8';
                checkMatch();
            });

            // Match indicator
            const confirmInput = document.getElementById('password_confirmation');
            const matchIndicator = document.getElementById('match-indicator');

            function checkMatch() {
                const pw = passwordInput?.value;
                const conf = confirmInput?.value;
                if (!conf) { matchIndicator.classList.add('hidden'); return; }
                matchIndicator.classList.remove('hidden');
                if (pw === conf) {
                    matchIndicator.textContent = '✓ Kata sandi cocok';
                    matchIndicator.style.color = '#0A7660';
                } else {
                    matchIndicator.textContent = '✗ Kata sandi tidak cocok';
                    matchIndicator.style.color = '#ef4444';
                }
            }

            confirmInput?.addEventListener('input', checkMatch);
        })();
    </script>
</x-layouts.auth>
