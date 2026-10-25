<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Services\Auth\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * PUB-05 / FR-AUTH-001. Melayani registrasi peran customer & mitra (bukan admin).
 */
class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request, OtpService $otpService): RedirectResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'no_hp' => $validated['no_hp'],
            // Role diambil dari whitelist Form Request (customer|mitra), bukan request mentah.
            'role' => $validated['role'],
        ]);

        // FR-AUTH-002: mulai verifikasi OTP setelah akun dibuat.
        $otpService->issue($user, 'email');

        session(['otp_user_id' => $user->id]);

        return redirect()->route('otp.verify.show')
            ->with('status', 'Akun berhasil dibuat. Masukkan kode OTP yang dikirim ke email Anda.');
    }
}
