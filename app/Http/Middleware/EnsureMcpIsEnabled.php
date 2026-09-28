<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Settings\GeneralSettings;
use Symfony\Component\HttpFoundation\Response;

class EnsureMcpIsEnabled
{
    /**
     * Hide the MCP server when an admin has not enabled it in the settings.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(app(GeneralSettings::class)->enable_mcp, 404);

        return $next($request);
    }
}
