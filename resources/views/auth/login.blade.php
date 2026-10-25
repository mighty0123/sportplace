<x-layouts.auth
    title="Masuk — SportPlace"
    badge="AUTENTIKASI AKUN"
    heading="Masuk ke Akun Anda"
    subheading="Akses dashboard mitra atau reservasi lapangan olahraga favoritmu."
>
    <form method="POST" action="{{ route('login.store') }}" class="space-y-4.5">
        @csrf

        <!-- Email Field -->
        <div>
            <x-input-label for="email" value="Alamat Email" required />
            <x-text-input
                type="email"
                name="email"
                id="email"
                :value="old('email')"
                :hasError="$errors->has('email')"
                placeholder="contoh@sportplace.com"
                required
                autofocus
            />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <!-- Password Field -->
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <x-input-label for="password" value="Password" required class="mb-0" />
                <a href="{{ route('password.request') }}" class="text-xs font-semibold text-[#0B7B69] hover:text-[#086354] transition hover:underline">
                    Lupa password?
                </a>
            </div>
            <x-text-input
                type="password"
                name="password"
                id="password"
                :hasError="$errors->has('password')"
                placeholder="Masukkan kata sandi minimal 8 karakter"
                required
            />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <!-- Remember Me Checkbox -->
        <div class="flex items-center pt-1">
            <label class="flex items-center gap-2.5 cursor-pointer text-sm text-slate-600 select-none">
                <input
                    type="checkbox"
                    name="remember"
                    class="w-4 h-4 rounded-md border-slate-300 text-[#0B7B69] focus:ring-[#0B7B69] cursor-pointer"
                >
                <span>Ingat saya di perangkat ini</span>
            </label>
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <x-primary-button>
                <span>Masuk Sekarang</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </x-primary-button>
        </div>
    </form>

    <div class="mt-6 pt-5 border-t border-slate-100 text-center">
        <p class="text-sm text-slate-600">
            Belum memiliki akun SportPlace?
            <a href="{{ route('register') }}" class="font-semibold text-[#0B7B69] hover:text-[#086354] transition hover:underline">
                Daftar sekarang
            </a>
        </p>
    </div>
</x-layouts.auth>
