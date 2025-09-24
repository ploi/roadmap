<?php

namespace App\Http\Middleware;

use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Closure;
use Illuminate\Http\Request;

class Localize
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request):((Response|RedirectResponse)) $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            app()->setLocale(auth()->user()->locale ?? config('app.locale'));
        }

        return $next($request);
    }
}
