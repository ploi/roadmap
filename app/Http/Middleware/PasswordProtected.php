<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Settings\GeneralSettings;

class PasswordProtected
{
    public function handle(Request $request, Closure $next)
    {
        if (
            app(GeneralSettings::class)->password &&
            !session('password-login-authorized') &&
            $request->route()->getName() !== 'password.protection' &&
            $request->route()->getName() !== 'password.protection.login'
        ) {
            return redirect()->route('password.protection');
        }

        return $next($request);
    }
}
