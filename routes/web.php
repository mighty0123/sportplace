<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\OtpVerificationController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Mitra\DashboardController;
use App\Http\Controllers\Mitra\OnboardingController;
use App\Http\Controllers\Mitra\VenueController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('user-public.welcome');
})->name('home');

Route::redirect('/beranda', '/')->name('beranda');

// FR-AUTH-001..005 (PUB-04/05/06). Dipakai bersama 3 peran — lihat CLAUDE.md §1.
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');

    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');

    // FR-AUTH-002 (T1.2). Rate limit percobaan verifikasi & kirim ulang (NFR-REC-001).
    Route::get('/otp/verify', [OtpVerificationController::class, 'show'])->name('otp.verify.show');
    Route::post('/otp/verify', [OtpVerificationController::class, 'verify'])
        ->middleware('throttle:10,1')
        ->name('otp.verify');
    Route::post('/otp/resend', [OtpVerificationController::class, 'resend'])
        ->middleware('throttle:5,1')
        ->name('otp.resend');

    // FR-AUTH-004 (T1.4). Lupa & reset password.
    Route::get('/forgot-password', [PasswordResetController::class, 'forgotForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.send-reset-link');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'resetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'updatePassword'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    // Mendukung POST (standar form) dan GET (untuk kemudahan navigasi URL browser)
    Route::match(['get', 'post'], '/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

// Area Mitra — Gerbang akses: MTR-01, MTR-02 terbuka untuk semua status mitra.
// MTR-03 ke atas wajib mitra.approved (EC-17, BR-002, PRD §12.2/§12.6).
Route::middleware(['auth', 'role:mitra'])->prefix('mitra')->name('mitra.')->group(function () {
    // MTR-01: Pendaftaran Mitra
    Route::get('/pendaftaran', [OnboardingController::class, 'create'])->name('onboarding.create');
    Route::post('/pendaftaran', [OnboardingController::class, 'store'])->name('onboarding.store');

    // MTR-02: Status Verifikasi Akun
    Route::get('/status', [OnboardingController::class, 'showStatus'])->name('status');

    // MTR-12: Edit & Ajukan Ulang jika ditolak
    Route::get('/resubmit', [OnboardingController::class, 'editResubmit'])->name('resubmit');
    Route::post('/resubmit', [OnboardingController::class, 'resubmit'])->name('resubmit.store');

    // Akses Dokumen Privat Terproteksi
    Route::get('/dokumen/{type}', [OnboardingController::class, 'downloadDocument'])->name('document.download');

    Route::middleware('mitra.approved')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // MTR-04 & MTR-05b: Manajemen Venue (FR-VENUE-001..004)
        Route::get('/venues', [VenueController::class, 'index'])->name('venues.index');
        Route::get('/venues/create', [VenueController::class, 'create'])->name('venues.create');
        Route::post('/venues', [VenueController::class, 'store'])->name('venues.store');
        Route::get('/venues/{venue}/edit', [VenueController::class, 'edit'])->name('venues.edit');
        Route::put('/venues/{venue}', [VenueController::class, 'update'])->name('venues.update');
        Route::delete('/venues/{venue}', [VenueController::class, 'destroy'])->name('venues.destroy');
        Route::post('/venues/{venue}/submit', [VenueController::class, 'submitForReview'])->name('venues.submit');
        Route::post('/venues/{venue}/deactivate', [VenueController::class, 'deactivate'])->name('venues.deactivate');
        Route::post('/venues/{venue}/activate', [VenueController::class, 'activate'])->name('venues.activate');
    });
});
