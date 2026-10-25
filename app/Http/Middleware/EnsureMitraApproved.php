<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMitraApproved
{
    /**
     * Handle an incoming request for protected mitra areas (MTR-03..MTR-11).
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->role !== 'mitra') {
            abort(403, 'Hanya mitra yang dapat mengakses halaman ini.');
        }

        $profile = $user->mitraProfile;

        if (! $profile) {
            return redirect()->route('mitra.onboarding.create')
                ->with('status', 'Silakan lengkapi formulir pendaftaran mitra terlebih dahulu.');
        }

        if ($profile->status !== 'approved') {
            // EC-17 / BR-002: akun mitra belum disetujui dialihkan ke MTR-02 (status verifikasi)
            return redirect()->route('mitra.status')
                ->with('warning', 'Akun mitra Anda masih menunggu verifikasi.');
        }

        return $next($request);
    }
}

