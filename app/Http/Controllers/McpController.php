<?php

namespace App\Http\Controllers;

use Laravel\Mcp\Server\Tool;
use App\Settings\GeneralSettings;
use App\Mcp\Servers\RoadmapServer;
use Illuminate\Contracts\View\View;

class McpController extends Controller
{
    /**
     * Explain how to connect to the MCP server, admins can read this before enabling it.
     */
    public function __invoke(): View
    {
        $enabled = app(GeneralSettings::class)->enable_mcp;

        abort_unless($enabled || auth()->user()?->hasAdminAccess(), 404);

        return view('mcp', [
            'enabled' => $enabled,
            'tools' => collect(RoadmapServer::TOOLS)->map(fn (string $tool): Tool => app($tool)),
        ]);
    }
}
