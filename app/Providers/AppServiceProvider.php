<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

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
        if (! $this->app->runningInConsole()) {
            $request = request();
            if ($request) {
                URL::forceRootUrl($request->root());
            }
        }

        RateLimiter::for('tenant-login', function (Request $request): Limit {
            $email = Str::lower((string) $request->input('email', ''));
            $schoolCode = Str::lower((string) $request->input('school_code', ''));

            return Limit::perMinute(5)->by($request->ip().'|tenant|'.$schoolCode.'|'.$email);
        });

        RateLimiter::for('superadmin-login', function (Request $request): Limit {
            $email = Str::lower((string) $request->input('email', ''));

            return Limit::perMinute(5)->by($request->ip().'|superadmin|'.$email);
        });
    }
}
