<?php

use Laravel\Mcp\Facades\Mcp;
use App\Mcp\Servers\RoadmapServer;
use App\Http\Middleware\EnsureMcpIsEnabled;

Mcp::web('/mcp', RoadmapServer::class)->middleware([EnsureMcpIsEnabled::class, 'auth:sanctum', 'throttle:api']);
