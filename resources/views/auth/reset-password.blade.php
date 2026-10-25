<x-layouts.auth
    title="Reset Password — SportPlace"
    badge="PEMBARUAN SANDI"
    heading="Buat Sandi Baru"
    subheading="Pastikan kata sandi baru Anda minimal 8 karakter dan mudah Anda ingat namun sulit ditebak."
>
    <form method="POST" action="{{ route('password.update') }}" class="space-y-4.5">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">

        <!-- Email Static Indicator -->
        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
            <span class="text-xs text-slate-500 block mb-0.5">Mereset kata sandi untuk akun:</span>
            <span class="text-sm font-semibold text-slate-800">{{ $email }}</span>
        </div>

        <!-- New Password Field -->
        <div>
            <x-input-label for="password" value="Kata Sandi Baru" required />
            <x-text-input
                type="password"
                name="password"
                id="password"
                :hasError="$errors->has('password')"
                placeholder="Minimal 8 karakter"
                minlength="8"
                required
                autofocus
            />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <!-- Confirm Password Field -->
        <div>
            <x-input-label for="password_confirmation" value="Ulangi Kata Sandi Baru" required />
            <x-text-input
                type="password"
                name="password_confirmation"
                id="password_confirmation"
                :hasError="$errors->has('password_confirmation')"
                placeholder="Ketik ulang kata sandi baru"
                minlength="8"
                required
            />
            <x-input-error :messages="$errors->get('password_confirmation')" />
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <x-primary-button>
                <span>Perbarui Kata Sandi</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </x-primary-button>
        </div>
    </form>
</x-layouts.auth>
