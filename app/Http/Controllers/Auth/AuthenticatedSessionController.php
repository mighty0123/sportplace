<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * PUB-04 / FR-AUTH-003. Login + redirect berdasarkan peran (§6 Peta Halaman Mitra).
 */
class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->ensureIsNotRateLimited();

        $credentials = $request->only('email', 'password');
        $loginInput = $credentials['email'];
        $user = User::where('email', $loginInput)
            ->orWhere('no_hp', $loginInput)
            ->first();

        // Pesan galat generik: tidak membedakan email tidak ada vs password salah (Usulan).
        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            $request->hitRateLimiter();

            throw ValidationException::withMessages([
                'email' => 'Email atau password salah.',
            ]);
        }

        // BR-018 (GAP-08): akun suspend/banned ditolak dengan pesan status.
        if ($user->status_akun !== 'aktif') {
            $request->hitRateLimiter();

            $pesan = $user->status_akun === 'banned'
                ? 'Akun Anda telah diblokir. Hubungi admin untuk informasi lebih lanjut.'
                : 'Akun Anda sedang disuspend. Hubungi admin untuk informasi lebih lanjut.';

            throw ValidationException::withMessages(['email' => $pesan]);
        }

        // Akun belum verifikasi OTP → arahkan ke layar OTP (bukan login).
        if (! $user->email_verified_at) {
            $request->session()->put('otp_user_id', $user->id);

            return redirect()->route('otp.verify.show')
                ->with('status', 'Akun belum terverifikasi. Masukkan kode OTP yang dikirim ke email Anda.');
        }

        $request->clearRateLimiter();
        $request->session()->regenerate();

        Auth::login($user, $request->boolean('remember'));

        return redirect()->intended($this->redirectPathFor($user));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * FR-AUTH-003 alur utama #4: redirect berdasarkan role & status mitra_profiles.
     */
    private function redirectPathFor(User $user): string
    {
        if ($user->role === 'admin') {
            // ADM-01 belum ada — placeholder, TODO untuk pemilik modul Admin.
            return '/admin';
        }

        if ($user->role === 'mitra') {
            $profile = $user->mitraProfile;

            if (! $profile) {
                return route('mitra.onboarding.create', absolute: false);
            }

            return match ($profile->status) {
                'approved' => route('mitra.dashboard', absolute: false),
                default => route('mitra.status', absolute: false), // pending, rejected
            };
        }

        // customer → REN-01 belum ada — placeholder, TODO untuk pemilik modul Penyewa.
        return '/beranda';
    }
}
