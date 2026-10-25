<x-layouts.auth
    title="Verifikasi OTP — SportPlace"
    badge="VERIFIKASI AKUN"
    heading="Masukkan Kode OTP"
    subheading="Untuk keamanan akun Anda, masukkan 6 digit kode yang telah kami kirimkan ke email Anda."
>
    <div class="mb-5 p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-center">
        <span class="text-xs text-slate-500 block mb-0.5">Kode dikirim ke alamat email:</span>
        <span class="text-sm font-semibold text-slate-900">{{ $email }}</span>
    </div>

    <form method="POST" action="{{ route('otp.verify') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="kode" value="Kode Verifikasi (6 Digit)" required class="text-center" />
            <input
                type="text"
                name="kode"
                id="kode"
                inputmode="numeric"
                maxlength="6"
                placeholder="••••••"
                autocomplete="one-time-code"
                required
                autofocus
                class="w-full text-center text-2xl font-bold tracking-[0.5em] py-3 px-4 rounded-xl border {{ $errors->has('kode') ? 'border-rose-400 bg-rose-50/20 text-rose-900 focus:ring-rose-500/20' : 'border-slate-300 focus:border-[#0B7B69] focus:ring-[#0B7B69]/20' }} focus:outline-hidden focus:ring-3 transition tabular-nums"
            >
            <x-input-error :messages="$errors->get('kode')" class="text-center justify-center" />
        </div>

        <div>
            <x-primary-button>
                <span>Verifikasi Akun</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </x-primary-button>
        </div>
    </form>

    <div class="mt-6 pt-5 border-t border-slate-100 text-center">
        <p class="text-xs text-slate-500 mb-3">Tidak menerima kode verifikasi atau kode kedaluwarsa?</p>
        <form method="POST" action="{{ route('otp.resend') }}">
            @csrf
            <button
                type="submit"
                class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#0B7B69] hover:text-[#086354] transition hover:underline cursor-pointer"
            >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <span>Kirim Ulang Kode OTP</span>
            </button>
        </form>
    </div>
</x-layouts.auth>
