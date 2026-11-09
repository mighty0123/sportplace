<?php

namespace App\Providers;

use App\Contracts\OtpSender;
use App\Models\User;
use App\Services\Auth\MailOtpSender;
use App\Support\PendingCounts;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Driver pengirim OTP: Mengirim email via MailOtpSender (dan fallback ke log dev)
        $this->app->bind(OtpSender::class, MailOtpSender::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('components.admin.sidebar', function ($view) {
            $view->with('navCounts', PendingCounts::all());
        });

        // Izin moderasi tidak terikat ke model tertentu
        Gate::define('moderate-listings', fn (User $user) => $user->role === 'admin' && $user->status_akun === 'aktif');
        Gate::define('manage-master-data', fn (User $user) => $user->role === 'admin' && $user->status_akun === 'aktif');
        Gate::define('view-transactions', fn (User $user) => $user->role === 'admin' && $user->status_akun === 'aktif');
    }
}
