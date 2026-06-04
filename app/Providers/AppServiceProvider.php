<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Paksa HTTPS saat menggunakan ngrok atau di lingkungan production
        if (config('app.env') !== 'local' || str_contains(request()->getHost(), 'ngrok')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        Paginator::useBootstrapFive();

        \Illuminate\Support\Facades\View::composer(['layouts.admin', 'layouts.panitia', 'layouts.peserta'], function ($view) {
            if (\Illuminate\Support\Facades\Auth::check()) {
                $user = \Illuminate\Support\Facades\Auth::user();
                $view->with('notifications', $user->unreadNotifications()->latest()->take(5)->get());
                $view->with('unreadNotificationsCount', $user->unreadNotifications()->count());
            }
        });
    }
}
