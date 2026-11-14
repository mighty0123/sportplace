<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Pengguna yang akunnya ditangguhkan atau diblokir langsung dikeluarkan pada permintaan berikutnya,
 * walau sesinya masih berlaku
 */
class EnsureAccountIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->status_akun !== 'aktif') {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Permintaan Livewire atau JSON tidak bisa menampilkan halaman login; beri tahu dengan kode status
            if ($request->expectsJson() || $request->header('X-Livewire')) {
                abort(401, 'Akun tidak aktif.');
            }

            return redirect()->route('login')->withErrors([
                'email' => 'Akun ini sedang ditangguhkan atau diblokir. Hubungi dukungan SportPlace bila ada pertanyaan.',
            ]);
        }

        return $next($request);
    }
}