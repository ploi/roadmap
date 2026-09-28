<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Laravel\Fortify\Fortify;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * This app uses laravel/ui (Auth::routes()) for login, registration and
     * password resets, so we prevent Fortify from registering its own routes
     * (which would clash on /login, /logout, etc.). We only pull in Fortify's
     * two-factor authentication engine and register the routes we need in
     * routes/web.php.
     */
    public function register(): void
    {
        Fortify::ignoreRoutes();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // View shown after a valid password when the account has 2FA enabled.
        Fortify::twoFactorChallengeView(fn () => view('auth.two-factor-challenge'));

        // Named limiter referenced by the two-factor-challenge route
        // (config('fortify.limiters.two-factor')). Keyed by the challenged
        // user id stashed in the session by the login flow.
        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
