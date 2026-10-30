<?php

namespace App\Providers;

use App\Support\PendingCounts;
use App\Contracts\OtpSender;
use App\Services\Auth\MailOtpSender;
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
    }
}
