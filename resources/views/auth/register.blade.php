<x-layouts.auth
    title="Daftar Akun — SportPlace"
    badge="REGISTRASI AKUN"
    heading="Buat Akun Baru"
    subheading="Pilih peranmu dan mulai perjalanan olahragamu bersama SportPlace."
>
    <form method="POST" action="{{ route('register.store') }}" class="space-y-4.5">
        @csrf

        <!-- Pilihan Peran Pengguna (Customer / Mitra) -->
        <div>
            <x-input-label value="Mendaftar Sebagai" required />
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-1.5" id="role-selector">
                <label id="label-role-customer" class="relative flex items-start gap-3 p-3.5 rounded-xl border-2 cursor-pointer transition select-none hover:bg-slate-50 {{ old('role', 'customer') === 'customer' ? 'border-[#0B7B69] bg-emerald-50/50 text-[#064E43]' : 'border-slate-200 text-slate-700' }}">
                    <input
                        type="radio"
                        name="role"
                        id="role-customer"
                        value="customer"
                        class="mt-0.5 w-4 h-4 text-[#0B7B69] focus:ring-[#0B7B69] border-slate-300 cursor-pointer"
                        {{ old('role', 'customer') === 'customer' ? 'checked' : '' }}
                        onchange="updateRoleSelection('customer')"
                    >
                    <div class="flex-1">
                        <div class="text-xs font-bold uppercase tracking-wider flex items-center justify-between">
                            <span>Penyewa</span>
                            <span class="text-base">🏸</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-0.5 font-normal leading-tight">Booking & sewa lapangan olahraga</p>
                    </div>
                </label>

                <label id="label-role-mitra" class="relative flex items-start gap-3 p-3.5 rounded-xl border-2 cursor-pointer transition select-none hover:bg-slate-50 {{ old('role') === 'mitra' ? 'border-[#0B7B69] bg-emerald-50/50 text-[#064E43]' : 'border-slate-200 text-slate-700' }}">
                    <input
                        type="radio"
                        name="role"
                        id="role-mitra"
                        value="mitra"
                        class="mt-0.5 w-4 h-4 text-[#0B7B69] focus:ring-[#0B7B69] border-slate-300 cursor-pointer"
                        {{ old('role') === 'mitra' ? 'checked' : '' }}
                        onchange="updateRoleSelection('mitra')"
                    >
                    <div class="flex-1">
                        <div class="text-xs font-bold uppercase tracking-wider flex items-center justify-between">
                            <span>Mitra Lapangan</span>
                            <span class="text-base">🏟️</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-0.5 font-normal leading-tight">Kelola venue & terima pesanan</p>
                    </div>
                </label>
            </div>
            <x-input-error :messages="$errors->get('role')" />
        </div>

        <script>
            function updateRoleSelection(selected) {
                const customerCard = document.getElementById('label-role-customer');
                const mitraCard = document.getElementById('label-role-mitra');

                if (selected === 'mitra') {
                    mitraCard.classList.add('border-[#0B7B69]', 'bg-emerald-50/50', 'text-[#064E43]');
                    mitraCard.classList.remove('border-slate-200', 'text-slate-700');

                    customerCard.classList.remove('border-[#0B7B69]', 'bg-emerald-50/50', 'text-[#064E43]');
                    customerCard.classList.add('border-slate-200', 'text-slate-700');
                } else {
                    customerCard.classList.add('border-[#0B7B69]', 'bg-emerald-50/50', 'text-[#064E43]');
                    customerCard.classList.remove('border-slate-200', 'text-slate-700');

                    mitraCard.classList.remove('border-[#0B7B69]', 'bg-emerald-50/50', 'text-[#064E43]');
                    mitraCard.classList.add('border-slate-200', 'text-slate-700');
                }
            }
        </script>

        <!-- Nama Lengkap -->
        <div>
            <x-input-label for="name" value="Nama Lengkap" required />
            <x-text-input
                type="text"
                name="name"
                id="name"
                :value="old('name')"
                :hasError="$errors->has('name')"
                placeholder="Contoh: Budi Santoso"
                maxlength="100"
                required
            />
            <x-input-error :messages="$errors->get('name')" />
        </div>

        <!-- Alamat Email -->
        <div>
            <x-input-label for="email" value="Alamat Email Aktif" required />
            <x-text-input
                type="email"
                name="email"
                id="email"
                :value="old('email')"
                :hasError="$errors->has('email')"
                placeholder="nama@email.com"
                maxlength="255"
                required
            />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <!-- Nomor Handphone / WhatsApp -->
        <div>
            <x-input-label for="no_hp" value="Nomor WhatsApp / HP" required />
            <x-text-input
                type="tel"
                name="no_hp"
                id="no_hp"
                :value="old('no_hp')"
                :hasError="$errors->has('no_hp')"
                placeholder="081234567890"
                maxlength="20"
                required
            />
            <x-input-error :messages="$errors->get('no_hp')" />
        </div>

        <!-- Password -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
            <div>
                <x-input-label for="password" value="Kata Sandi" required />
                <x-text-input
                    type="password"
                    name="password"
                    id="password"
                    :hasError="$errors->has('password')"
                    placeholder="Min. 8 karakter"
                    minlength="8"
                    required
                />
                <x-input-error :messages="$errors->get('password')" />
            </div>

            <div>
                <x-input-label for="password_confirmation" value="Konfirmasi Sandi" required />
                <x-text-input
                    type="password"
                    name="password_confirmation"
                    id="password_confirmation"
                    :hasError="$errors->has('password_confirmation')"
                    placeholder="Ulangi kata sandi"
                    minlength="8"
                    required
                />
                <x-input-error :messages="$errors->get('password_confirmation')" />
            </div>
        </div>

        <!-- Terms Note -->
        <p class="text-xs text-slate-500 pt-1 leading-relaxed">
            Dengan mendaftar, Anda menyetujui Ketentuan Layanan dan Kebijakan Privasi SportPlace.
        </p>

        <!-- Submit Button -->
        <div class="pt-2">
            <x-primary-button>
                <span>Daftar & Verifikasi OTP</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </x-primary-button>
        </div>
    </form>

    <div class="mt-6 pt-5 border-t border-slate-100 text-center">
        <p class="text-sm text-slate-600">
            Sudah memiliki akun?
            <a href="{{ route('login') }}" class="font-semibold text-[#0B7B69] hover:text-[#086354] transition hover:underline">
                Masuk di sini
            </a>
        </p>
    </div>
</x-layouts.auth>
