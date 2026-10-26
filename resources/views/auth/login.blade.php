<x-layouts.auth title="Masuk Akun — SportPlace">
    <!-- Main Card Container: 2-Column Split (Left Banner, Right Form) -->
    <div class="bg-white rounded-3xl border border-emerald-100 shadow-sm overflow-hidden flex flex-col lg:flex-row p-3 sm:p-4 lg:p-5 gap-6 lg:gap-8">
        
        <!-- Left Column: Branding Dark Banner Component with Stadium Background -->
        <x-auth.side-banner variant="login" />

        <!-- Right Column: Login Form -->
        <div class="w-full lg:w-7/12 p-4 sm:p-6 lg:p-10 flex flex-col justify-center">
            
            <!-- Form Header -->
            <div class="mb-8">
                <span class="inline-block text-xs font-bold uppercase tracking-wider text-[#0A7660] mb-2">
                    Akses Akun
                </span>
                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-[#1B2724]">
                    Selamat datang kembali
                </h2>
                <p class="text-xs sm:text-sm text-[#5A6C5B] mt-1.5 leading-relaxed">
                    Masuk untuk melanjutkan booking lapanganmu.
                </p>
            </div>

            <!-- Global Status / Notification Message -->
            @if (session('status'))
                <div class="mb-6 p-4 rounded-xl bg-[#E8F7EE] border border-[#BBEDE3] text-xs font-medium text-[#0A7660] flex items-start gap-2.5">
                    <svg class="w-4 h-4 shrink-0 mt-0.5 text-[#0A7660]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <!-- Form -->
            <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
                @csrf

                <!-- 1. Email atau Nomor HP -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-[#1B2724] mb-1.5">
                        Email atau Nomor HP <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#7D8C78]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <input 
                            type="text" 
                            name="email" 
                            id="email" 
                            value="{{ old('email') }}" 
                            placeholder="Masukkan email atau nomor HP" 
                            required 
                            autofocus
                            class="w-full pl-10 pr-4 py-3 rounded-xl bg-[#F2F7F4] border {{ $errors->has('email') ? 'border-red-400' : 'border-transparent' }} focus:bg-white focus:border-[#0A7660] focus:ring-2 focus:ring-[#0A7660]/20 text-sm text-[#1B2724] placeholder-[#7D8C78] transition outline-none"
                        />
                    </div>
                    @error('email')
                        <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 2. Password -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-[#1B2724] mb-1.5">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#7D8C78]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input 
                            type="password" 
                            name="password" 
                            id="password" 
                            placeholder="Masukkan kata sandi akun" 
                            required 
                            class="w-full pl-10 pr-10 py-3 rounded-xl bg-[#F2F7F4] border {{ $errors->has('password') ? 'border-red-400' : 'border-transparent' }} focus:bg-white focus:border-[#0A7660] focus:ring-2 focus:ring-[#0A7660]/20 text-sm text-[#1B2724] placeholder-[#7D8C78] transition outline-none"
                        />
                        <button 
                            type="button" 
                            onclick="togglePasswordVisibility('password', this)" 
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-[#7D8C78] hover:text-[#1B2724] transition"
                            aria-label="Toggle password visibility"
                        >
                            <svg class="w-4 h-4 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me & Forgot Password Row -->
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer text-xs font-medium text-[#5A6C5B] select-none">
                        <input 
                            type="checkbox" 
                            name="remember" 
                            id="remember" 
                            class="w-4 h-4 rounded text-[#0A7660] focus:ring-[#0A7660] border-slate-300 cursor-pointer"
                        />
                        <span>Ingat saya</span>
                    </label>

                    <a href="{{ route('password.request') }}" class="text-xs font-semibold text-[#0A7660] hover:text-[#0B604F] hover:underline transition">
                        Lupa password?
                    </a>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button 
                        type="submit" 
                        class="w-full flex items-center justify-center gap-2 py-3.5 px-6 rounded-xl bg-[#0A7660] hover:bg-[#0B604F] active:bg-[#0C4E40] text-white text-sm font-semibold transition shadow-sm"
                    >
                        <span>Masuk</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </button>
                </div>


                <!-- Link to Register -->
                <div class="pt-2 text-center text-xs text-[#5A6C5B]">
                    Belum punya akun? 
                    <a href="{{ route('register') }}" class="font-bold text-[#0A7660] hover:text-[#0B604F] hover:underline transition ml-1">
                        Daftar Sekarang &rsaquo;
                    </a>
                </div>

            </form>

        </div>
    </div>

    <!-- Client-side script for password visibility toggle -->
    <script>
        function togglePasswordVisibility(inputId, btn) {
            const input = document.getElementById(inputId);
            if (!input) return;

            if (input.type === 'password') {
                input.type = 'text';
                btn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>`;
            } else {
                input.type = 'password';
                btn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>`;
            }
        }
    </script>
</x-layouts.auth>
