<?php

namespace App\Providers;

use App\Contracts\OtpSender;
use App\Services\Auth\LogOtpSender;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Penyedia OTP belum ditentukan (DEP-02) — default log driver untuk dev.
        $this->app->bind(OtpSender::class, LogOtpSender::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
