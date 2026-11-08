<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Models\User;
use App\Services\Auth\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * FR-AUTH-002 (PUB-05 lanjutan, GAP-09). Verifikasi akun via OTP setelah registrasi.
 */
class OtpVerificationController extends Controller
{
    /**
     * Ambil user target verifikasi dari session yang diset saat registrasi.
     * Tidak mengandalkan auth guard karena user belum login (belum terverifikasi).
     */
    private function resolveUser(Request $request): ?User
    {
        $userId = $request->session()->get('otp_user_id');

        return $userId ? User::find($userId) : null;
    }

    public function show(Request $request): View|RedirectResponse
    {
        $user = $this->resolveUser($request);

        if (! $user) {
            return redirect()->route('register')
                ->with('status', 'Sesi verifikasi tidak ditemukan. Silakan daftar ulang.');
        }

        return view('auth.otp-verify', ['email' => $user->email]);
    }

    public function verify(VerifyOtpRequest $request, OtpService $otpService): RedirectResponse
    {
        $user = $this->resolveUser($request);

        if (! $user) {
            return redirect()->route('register')
                ->with('status', 'Sesi verifikasi tidak ditemukan. Silakan daftar ulang.');
        }

        $result = $otpService->verify($user, $request->validated('kode'), 'email');

        if (! $result['success']) {
            return back()->withErrors(['kode' => $result['message']]);
        }

        $request->session()->forget('otp_user_id');

        return redirect()->route('login')
            ->with('status', 'Verifikasi berhasil. Silakan masuk.')
            ->with('verified_email', $user->email);
    }

    public function resend(Request $request, OtpService $otpService): RedirectResponse
    {
        $user = $this->resolveUser($request);

        if (! $user) {
            return redirect()->route('register')
                ->with('status', 'Sesi verifikasi tidak ditemukan. Silakan daftar ulang.');
        }

        if ($otpService->inResendCooldown($user, 'email')) {
            return back()->withErrors(['kode' => 'Mohon tunggu sebelum meminta kode baru.']);
        }

        $otpService->issue($user, 'email');

        return back()->with('status', 'Kode OTP baru telah dikirim.');
    }
}
