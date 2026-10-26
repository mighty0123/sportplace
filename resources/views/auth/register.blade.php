<x-layouts.auth title="Pendaftaran Akun Baru — SportPlace">
    <!-- Main Card Container: 2-Column Split (Left Banner, Right Form) -->
    <div class="bg-white rounded-3xl border border-emerald-100 shadow-sm overflow-hidden flex flex-col lg:flex-row p-3 sm:p-4 lg:p-5 gap-6 lg:gap-8">
        
        <!-- Left Column: Branding Dark Banner Component -->
        <x-auth.side-banner variant="register" />

        <!-- Right Column: Registration Form -->
        <div class="w-full lg:w-7/12 p-4 sm:p-6 lg:p-8 flex flex-col justify-center">
            
            <!-- Form Header -->
            <div class="mb-6">
                <div class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#0A7660] mb-2">
                    <svg class="w-4 h-4 text-[#0A7660]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    <span>Pendaftaran Akun Baru</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-[#1B2724]">
                    Gabung di SportPlace
                </h2>
                <p class="text-xs sm:text-sm text-[#5A6C5B] mt-1">
                    Pilih peran akun untuk menyesuaikan fitur dashboard dan reservasi lapanganmu.
                </p>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('register.store') }}" class="space-y-4" id="register-form">
                @csrf

                <!-- 1. Dual Role Selector (Penyewa vs Mitra Venue) -->
                <div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" id="role-container">
                        <!-- Role: Penyewa (customer) -->
                        <x-auth.role-card 
                            value="customer"
                            title="Penyewa"
                            subtitle="Untuk atlet hobi, komunitas, & grup sparring."
                            :features="[
                                'Jelajahi 450+ venue multi-sport',
                                'Cek ketersediaan jam real-time',
                                'Fitur Split Bill & Reschedule aman'
                            ]"
                            icon="user"
                            :currentRole="old('role', 'customer')"
                        />

                        <!-- Role: Mitra Venue (mitra) -->
                        <x-auth.role-card 
                            value="mitra"
                            title="Mitra Venue"
                            subtitle="Untuk pengelola GOR, court futsal, & akademi."
                            :features="[
                                'Atur slot & harga dinamis',
                                'Pembayaran otomatis & terpusat',
                                'Jangkau ribuan atlet lokal'
                            ]"
                            icon="venue"
                            :currentRole="old('role', 'customer')"
                        />
                    </div>
                    @error('role')
                        <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 2. Nama Lengkap (Sesuai KTP) -->
                <div>
                    <label for="name" class="block text-xs font-semibold text-[#1B2724] mb-1.5">
                        Nama Lengkap (Sesuai KTP)
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#7D8C78]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <input 
                            type="text" 
                            name="name" 
                            id="name" 
                            value="{{ old('name') }}" 
                            placeholder="Budi Pratama" 
                            required 
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-[#F2F7F4] border {{ $errors->has('name') ? 'border-red-400' : 'border-transparent' }} focus:bg-white focus:border-[#0A7660] focus:ring-2 focus:ring-[#0A7660]/20 text-sm text-[#1B2724] placeholder-[#7D8C78] transition outline-none"
                        />
                    </div>
                    @error('name')
                        <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 3. Email Aktif -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-[#1B2724] mb-1.5">
                        Email Aktif
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#7D8C78]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <input 
                            type="email" 
                            name="email" 
                            id="email" 
                            value="{{ old('email') }}" 
                            placeholder="budi.pratama@email.com" 
                            required 
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-[#F2F7F4] border {{ $errors->has('email') ? 'border-red-400' : 'border-transparent' }} focus:bg-white focus:border-[#0A7660] focus:ring-2 focus:ring-[#0A7660]/20 text-sm text-[#1B2724] placeholder-[#7D8C78] transition outline-none"
                        />
                    </div>
                    @error('email')
                        <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 4. Nomor WhatsApp / HP Aktif with Flag -->
                <div>
                    <label for="no_hp" class="block text-xs font-semibold text-[#1B2724] mb-1.5">
                        Nomor WhatsApp / HP Aktif
                    </label>
                    <div class="flex rounded-xl bg-[#F2F7F4] border {{ $errors->has('no_hp') ? 'border-red-400' : 'border-transparent' }} focus-within:bg-white focus-within:border-[#0A7660] focus-within:ring-2 focus-within:ring-[#0A7660]/20 transition overflow-hidden">
                        <!-- Flag Prefix -->
                        <div class="flex items-center gap-1.5 px-3 py-2.5 bg-[#E8F7EE] border-r border-[#BBEDE3]/60 shrink-0 text-xs font-semibold text-[#1B2724]">
                            <!-- Indonesian Flag SVG -->
                            <span class="inline-block w-4 h-3 rounded-xs overflow-hidden shadow-2xs border border-slate-200">
                                <span class="block h-1.5 bg-red-600"></span>
                                <span class="block h-1.5 bg-white"></span>
                            </span>
                            <span>+62</span>
                        </div>
                        <input 
                            type="tel" 
                            name="no_hp" 
                            id="no_hp" 
                            value="{{ old('no_hp') }}" 
                            placeholder="81234567890" 
                            required 
                            class="w-full px-3.5 py-2.5 bg-transparent text-sm text-[#1B2724] placeholder-[#7D8C78] transition outline-none"
                        />
                    </div>
                    <p class="text-[11px] text-[#7D8C78] mt-1">
                        Kode OTP verifikasi akan dikirimkan ke nomor WhatsApp ini.
                    </p>
                    @error('no_hp')
                        <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 5. Kata Sandi & Konfirmasi Kata Sandi (2 Columns) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <!-- Kata Sandi -->
                    <div>
                        <label for="password" class="block text-xs font-semibold text-[#1B2724] mb-1.5">
                            Kata Sandi
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
                                placeholder="SportSpace2025!" 
                                required 
                                minlength="8" 
                                class="w-full pl-10 pr-10 py-2.5 rounded-xl bg-[#F2F7F4] border {{ $errors->has('password') ? 'border-red-400' : 'border-transparent' }} focus:bg-white focus:border-[#0A7660] focus:ring-2 focus:ring-[#0A7660]/20 text-sm text-[#1B2724] placeholder-[#7D8C78] transition outline-none"
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

                    <!-- Konfirmasi Kata Sandi -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-[#1B2724] mb-1.5">
                            Konfirmasi Kata Sandi
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#7D8C78]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                            </div>
                            <input 
                                type="password" 
                                name="password_confirmation" 
                                id="password_confirmation" 
                                placeholder="SportSpace2025!" 
                                required 
                                minlength="8" 
                                class="w-full pl-10 pr-10 py-2.5 rounded-xl bg-[#F2F7F4] border border-transparent focus:bg-white focus:border-[#0A7660] focus:ring-2 focus:ring-[#0A7660]/20 text-sm text-[#1B2724] placeholder-[#7D8C78] transition outline-none"
                            />
                            <button 
                                type="button" 
                                onclick="togglePasswordVisibility('password_confirmation', this)" 
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-[#7D8C78] hover:text-[#1B2724] transition"
                                aria-label="Toggle password confirmation visibility"
                            >
                                <svg class="w-4 h-4 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Password Requirements Bar -->
                <div class="p-2.5 rounded-xl bg-[#E8F7EE] border border-[#BBEDE3]/70 flex flex-wrap items-center justify-between gap-2 text-[11px] font-medium text-[#0A7660]">
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-[#0A7660]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Min. 8 karakter
                    </span>
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-[#0A7660]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Huruf besar & kecil
                    </span>
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-[#0A7660]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Kombinasi angka / simbol
                    </span>
                </div>

                <!-- Terms & Privacy Agreement -->
                <div class="flex items-start gap-2.5 pt-1">
                    <input 
                        type="checkbox" 
                        name="agree_terms" 
                        id="agree_terms" 
                        required 
                        checked 
                        class="mt-1 w-4 h-4 rounded text-[#0A7660] focus:ring-[#0A7660] border-slate-300 cursor-pointer"
                    />
                    <label for="agree_terms" class="text-xs text-[#5A6C5B] leading-snug cursor-pointer select-none">
                        Saya menyetujui <a href="#" class="font-semibold text-[#0A7660] hover:underline">Ketentuan Layanan</a>, <a href="#" class="font-semibold text-[#0A7660] hover:underline">Kebijakan Privasi</a>, serta menerima pembaruan jadwal lapangan melalui WhatsApp resmi SportPlace.
                    </label>
                </div>

                <!-- Submit CTA Button (Dynamic Label) -->
                <div class="pt-2">
                    <button 
                        type="submit" 
                        id="btn-submit-register" 
                        class="w-full flex items-center justify-center gap-2 py-3 px-6 rounded-xl bg-[#0A7660] hover:bg-[#0B604F] active:bg-[#0C4E40] text-white text-sm font-semibold transition shadow-sm"
                    >
                        <span id="btn-submit-text">Daftar sebagai Penyewa</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </button>
                </div>


                <!-- Bottom Link to Login -->
                <div class="pt-2 text-center text-xs text-[#5A6C5B]">
                    Sudah terdaftar di SportPlace? 
                    <a href="{{ route('login') }}" class="font-bold text-[#0A7660] hover:text-[#0B604F] hover:underline transition ml-1">
                        Masuk ke akunmu
                    </a>
                </div>

            </form>

        </div>
    </div>

    <!-- Client-side script for dynamic role card selection & password visibility -->
    <script>
        function handleRoleChange(selectedRole) {
            const customerCard = document.getElementById('role-card-customer');
            const mitraCard = document.getElementById('role-card-mitra');
            const customerCheck = document.getElementById('check-badge-customer');
            const mitraCheck = document.getElementById('check-badge-mitra');
            const submitText = document.getElementById('btn-submit-text');

            if (selectedRole === 'mitra') {
                mitraCard.classList.add('bg-[#E8F7EE]', 'border-[#0A7660]', 'shadow-xs');
                mitraCard.classList.remove('bg-[#F2F7F4]', 'border-transparent');
                mitraCheck.classList.remove('opacity-0');
                mitraCheck.classList.add('opacity-100');

                customerCard.classList.remove('bg-[#E8F7EE]', 'border-[#0A7660]', 'shadow-xs');
                customerCard.classList.add('bg-[#F2F7F4]', 'border-transparent');
                customerCheck.classList.remove('opacity-100');
                customerCheck.classList.add('opacity-0');

                if (submitText) submitText.textContent = 'Daftar sebagai Mitra Venue';
            } else {
                customerCard.classList.add('bg-[#E8F7EE]', 'border-[#0A7660]', 'shadow-xs');
                customerCard.classList.remove('bg-[#F2F7F4]', 'border-transparent');
                customerCheck.classList.remove('opacity-0');
                customerCheck.classList.add('opacity-100');

                mitraCard.classList.remove('bg-[#E8F7EE]', 'border-[#0A7660]', 'shadow-xs');
                mitraCard.classList.add('bg-[#F2F7F4]', 'border-transparent');
                mitraCheck.classList.remove('opacity-100');
                mitraCheck.classList.add('opacity-0');

                if (submitText) submitText.textContent = 'Daftar sebagai Penyewa';
            }
        }

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
