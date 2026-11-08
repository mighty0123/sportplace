<x-layouts.auth title="Lupa Password — SportPlace">
    <div class="bg-white rounded-3xl border border-emerald-100 shadow-sm overflow-hidden flex flex-col lg:flex-row p-3 sm:p-4 lg:p-5 gap-6 lg:gap-8">

        <!-- ===== LEFT: Banner ===== -->
        <div class="relative w-full lg:w-5/12 rounded-2xl overflow-hidden min-h-[280px] lg:min-h-full flex flex-col justify-between p-8 sm:p-10 bg-gradient-to-b from-[#06231D] via-[#0B322A] to-[#06231D]">
            <!-- Decorative glows -->
            <div class="absolute -top-16 -right-16 w-64 h-64 bg-[#0A7660]/30 blur-3xl pointer-events-none rounded-full"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-[#DEEF25]/10 blur-3xl pointer-events-none rounded-full"></div>

            <!-- Icon + Headline -->
            <div class="relative z-10 space-y-6">
                <!-- Lock Icon -->
                <div class="w-16 h-16 rounded-2xl bg-[#0A7660]/40 border border-[#43E5C5]/20 flex items-center justify-center">
                    <svg class="w-8 h-8 text-[#DEEF25]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                </div>

                <div class="space-y-3">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#0C4E40]/80 border border-[#43E5C5]/20 text-[#DEEF25] text-xs font-bold tracking-wider">
                        PEMULIHAN AKUN
                    </div>
                    <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-white leading-tight">
                        Lupa Kata<br>
                        <span class="text-[#DEEF25]">Sandi?</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-emerald-100/80 leading-relaxed">
                        Jangan khawatir. Masukkan email terdaftar dan kami kirimkan tautan untuk membuat kata sandi baru.
                    </p>
                </div>
            </div>

            <!-- Bottom: Steps -->
            <div class="relative z-10 pt-10 space-y-4">
                <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-400/80">Cara Kerja</p>

                <div class="flex items-start gap-3">
                    <div class="w-6 h-6 rounded-full bg-[#DEEF25] text-[#06231D] flex items-center justify-center shrink-0 text-[10px] font-black mt-0.5">1</div>
                    <p class="text-xs text-emerald-100/70 leading-snug">Masukkan <strong class="text-white">alamat email</strong> yang terdaftar di SportPlace</p>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-6 h-6 rounded-full bg-[#0A7660]/60 border border-[#43E5C5]/30 text-emerald-300 flex items-center justify-center shrink-0 text-[10px] font-black mt-0.5">2</div>
                    <p class="text-xs text-emerald-100/70 leading-snug">Kami kirimkan <strong class="text-white">tautan reset</strong> ke email Anda</p>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-6 h-6 rounded-full bg-[#0A7660]/60 border border-[#43E5C5]/30 text-emerald-300 flex items-center justify-center shrink-0 text-[10px] font-black mt-0.5">3</div>
                    <p class="text-xs text-emerald-100/70 leading-snug">Klik tautan dan buat <strong class="text-white">kata sandi baru</strong></p>
                </div>
            </div>
        </div>

        <!-- ===== RIGHT: Form Panel ===== -->
        <div class="w-full lg:w-7/12 p-4 sm:p-6 lg:p-10 flex flex-col justify-center">

            <!-- Form Header -->
            <div class="mb-8">
                <div class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#0A7660] mb-2">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span>Reset via Email</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-[#1B2724]">
                    Kirim Tautan Reset
                </h2>
                <p class="text-xs sm:text-sm text-[#5A6C5B] mt-1.5 leading-relaxed">
                    Masukkan alamat email yang Anda gunakan saat mendaftar. Tautan reset berlaku selama 60 menit.
                </p>
            </div>

            <!-- Success Status -->
            @if (session('status'))
                <div class="mb-6 p-4 rounded-2xl bg-[#E8F7EE] border border-[#BAEECD] flex items-start gap-3">
                    <div class="w-8 h-8 rounded-xl bg-[#0A7660] flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-[#0A7660]">Email Terkirim!</p>
                        <p class="text-xs text-[#0A7660]/80 mt-0.5">{{ session('status') }}</p>
                    </div>
                </div>
            @endif

            <!-- Form -->
            <form method="POST" action="{{ route('password.send-reset-link') }}" class="space-y-5">
                @csrf

                <!-- Email Field -->
                <div class="space-y-1.5">
                    <label for="email" class="block text-xs font-semibold text-[#1B2724]">
                        Alamat Email Terdaftar <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-[#7D8C78]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            value="{{ old('email') }}"
                            placeholder="nama@email.com"
                            required
                            autofocus
                            class="w-full pl-10 pr-4 py-3 text-sm rounded-xl border-2 {{ $errors->has('email') ? 'border-rose-400 bg-rose-50/20 text-rose-900 focus:ring-rose-500/20 focus:border-rose-400' : 'border-[#D7E9DF] bg-[#F7FBF9] text-[#1B2724] focus:border-[#0A7660] focus:ring-[#0A7660]/10' }} focus:outline-none focus:ring-4 transition placeholder-[#9DB0A8]"
                        />
                    </div>
                    @error('email')
                        <p class="text-xs text-rose-600 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Info Note -->
                <div class="flex items-start gap-2.5 p-3.5 rounded-xl bg-[#F1F9F7] border border-[#BBEDE3]/70 text-xs text-[#5A6C5B]">
                    <svg class="w-4 h-4 text-[#0A7660] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Tautan reset akan kedaluwarsa dalam <strong class="font-semibold text-[#1B2724]">60 menit</strong>. Segera periksa kotak masuk email Anda, termasuk folder Spam.</span>
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    id="btn-send-reset"
                    class="w-full flex items-center justify-center gap-2 py-3.5 px-6 rounded-xl bg-[#0A7660] hover:bg-[#0B604F] active:bg-[#0C4E40] text-white text-sm font-semibold transition shadow-sm"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span>Kirim Tautan Reset</span>
                </button>
            </form>

            <!-- Back to Login -->
            <div class="mt-8 text-center">
                <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#5A6C5B] hover:text-[#0A7660] hover:underline transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke halaman Masuk
                </a>
            </div>
        </div>
    </div>
</x-layouts.auth>
