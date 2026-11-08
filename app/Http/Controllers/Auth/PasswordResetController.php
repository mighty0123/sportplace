<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * FR-AUTH-004 (PUB-06). Lupa & reset password.
 */
class PasswordResetController extends Controller
{
    public function forgotForm(): View
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(ForgotPasswordRequest $request): RedirectResponse
    {
        $user = User::where('email', $request->email)->first();

        // Response netral: jangan membedakan email ada atau tidak (cegah enumerasi).
        if (! $user) {
            return back()->with('status', 'Jika email terdaftar, tautan reset password telah dikirim.');
        }

        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        // TODO: kirim email dengan tautan reset (DEP-02, INT-03 belum ditentukan).
        // Sementara: catat token di log untuk dev/testing.
        Log::info('Password reset token', [
            'email' => $user->email,
            'token' => $token,
        ]);

        return back()->with('status', 'Jika email terdaftar, tautan reset password telah dikirim.');
    }

    public function resetForm(Request $request, string $token): View|RedirectResponse
    {
        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (! $record || ! Hash::check($token, $record->token)) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Tautan reset password tidak valid atau sudah kedaluwarsa.']);
        }

        return view('auth.reset-password', ['email' => $request->email, 'token' => $token]);
    }

    public function updatePassword(ResetPasswordRequest $request): RedirectResponse
    {
        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (! $record) {
            return back()->withErrors(['email' => 'Tautan reset password tidak valid atau sudah kedaluwarsa.']);
        }

        if (! Hash::check($request->token, $record->token)) {
            return back()->withErrors(['email' => 'Tautan reset password tidak valid atau sudah kedaluwarsa.']);
        }

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return back()->withErrors(['email' => 'Email tidak terdaftar.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')
            ->with('status', 'Password berhasil diubah. Silakan masuk dengan password baru.');
    }
}
