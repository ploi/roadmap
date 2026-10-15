<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Settings\GeneralSettings;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasskeysAreEnabled
{
    /**
     * Hide the passkey endpoints when an admin has not enabled passkeys in the settings.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(app(GeneralSettings::class)->enable_passkeys, 404);

        return $next($request);
    }
}
