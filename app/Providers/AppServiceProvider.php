<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        App::setLocale('id');
        Carbon::setLocale('id');

        RateLimiter::for('invitation-rsvp', function (Request $request): array {
            $guestCode = (string) $request->input('guest_code', '');

            return [
                Limit::perMinute(10)->by($request->ip()),
                Limit::perMinute(5)->by($request->ip().'|'.$guestCode),
            ];
        });

        RateLimiter::for('invitation-wish', function (Request $request): array {
            $guestCode = (string) $request->input('guest_code', '');

            return [
                Limit::perMinute(6)->by($request->ip()),
                Limit::perMinute(4)->by($request->ip().'|'.$guestCode),
            ];
        });
    }
}
