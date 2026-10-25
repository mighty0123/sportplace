<x-layouts.auth
    title="Lupa Password — SportPlace"
    badge="PEMULIHAN AKUN"
    heading="Lupa Kata Sandi?"
    subheading="Jangan khawatir. Masukkan alamat email akun Anda dan kami akan mengirimkan tautan untuk membuat kata sandi baru."
>
    <form method="POST" action="{{ route('password.send-reset-link') }}" class="space-y-4.5">
        @csrf

        <!-- Email Field -->
        <div>
            <x-input-label for="email" value="Alamat Email Terdaftar" required />
            <x-text-input
                type="email"
                name="email"
                id="email"
                :value="old('email')"
                :hasError="$errors->has('email')"
                placeholder="nama@email.com"
                required
                autofocus
            />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div class="pt-2">
            <x-primary-button>
                <span>Kirim Tautan Reset</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </x-primary-button>
        </div>
    </form>

    <div class="mt-6 pt-5 border-t border-slate-100 text-center">
        <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-[#0B7B69] transition hover:underline">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali ke halaman Masuk</span>
        </a>
    </div>
</x-layouts.auth>
