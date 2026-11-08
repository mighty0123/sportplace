<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\MitraVerificationController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\OtpVerificationController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Mitra\DashboardController as MitraDashboardController;
use App\Http\Controllers\Mitra\FieldController;
use App\Http\Controllers\Mitra\OnboardingController;
use App\Http\Controllers\Mitra\OrderController;
use App\Http\Controllers\Mitra\PricingController;
use App\Http\Controllers\Mitra\ScheduleController;
use App\Http\Controllers\Mitra\VenueController;
use App\Models\MitraProfile;
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
        Route::get('/dashboard', [MitraDashboardController::class, 'index'])->name('dashboard');

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

        // MTR-04 & MTR-05: Manajemen Lapangan (FR-FIELD-001..004)
        Route::get('/venues/{venue}/fields', [FieldController::class, 'index'])->name('venues.fields.index');
        Route::get('/venues/{venue}/fields/create', [FieldController::class, 'create'])->name('venues.fields.create');
        Route::post('/venues/{venue}/fields', [FieldController::class, 'store'])->name('venues.fields.store');
        Route::get('/fields', [FieldController::class, 'allFields'])->name('fields.all');
        Route::get('/fields/{field}/edit', [FieldController::class, 'edit'])->name('fields.edit');
        Route::put('/fields/{field}', [FieldController::class, 'update'])->name('fields.update');
        Route::delete('/fields/{field}', [FieldController::class, 'destroy'])->name('fields.destroy');
        Route::post('/fields/{field}/submit', [FieldController::class, 'submitForReview'])->name('fields.submit');
        Route::post('/fields/{field}/toggle-status', [FieldController::class, 'toggleStatus'])->name('fields.toggle-status');
        Route::delete('/fields/{field}/photos/{photo}', [FieldController::class, 'deletePhoto'])->name('fields.photos.destroy');
        Route::post('/fields/{field}/photos/{photo}/set-primary', [FieldController::class, 'setPrimaryPhoto'])->name('fields.photos.set-primary');

        // MTR-06: Jadwal Operasional & Blokir Lapangan (FR-SCHED-001..002)
        Route::get('/jadwal', [ScheduleController::class, 'index'])->name('schedules.index');
        Route::put('/fields/{field}/schedule', [ScheduleController::class, 'update'])->name('fields.schedule.update');
        Route::post('/fields/{field}/blocks', [ScheduleController::class, 'storeBlock'])->name('fields.blocks.store');
        Route::delete('/fields/{field}/blocks/{block}', [ScheduleController::class, 'destroyBlock'])->name('fields.blocks.destroy');
        Route::get('/fields/{field}/slots-json', [ScheduleController::class, 'slotsJson'])->name('fields.slots.json');

        // MTR-07: Pengaturan Harga Peak / Off-Peak (FR-FIELD-006)
        Route::get('/harga', [PricingController::class, 'index'])->name('pricing.index');
        Route::put('/fields/{field}/pricing', [PricingController::class, 'update'])->name('fields.pricing.update');

        // MTR-08, MTR-09, MTR-10: Pesanan Mitra (FR-ORDER-001..003)
        Route::get('/pesanan', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/pesanan/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::post('/pesanan/{order}/confirm-attendance', [OrderController::class, 'confirmAttendance'])->name('orders.confirm-attendance');
        Route::post('/pesanan/{order}/no-show', [OrderController::class, 'markNoShow'])->name('orders.no-show');
        Route::post('/pesanan/{order}/complete', [OrderController::class, 'complete'])->name('orders.complete');
        Route::post('/pesanan-bulk-complete', [OrderController::class, 'bulkComplete'])->name('orders.bulk-complete');
    });
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', AdminDashboardController::class)->name('dashboard');

    Route::prefix('verifikasi')->name('verifikasi.')->group(function () {
        Route::post('/{mitra}/setujui', [MitraVerificationController::class, 'approve'])->name('approve');
        Route::post('/{mitra}/tolak', [MitraVerificationController::class, 'reject'])->name('reject');
        Route::get('/{mitra}/dokumen/{type}', [MitraVerificationController::class, 'document'])
            ->whereIn('type', array_keys(MitraProfile::DOCUMENTS))
            ->name('document');
    });
});
