<?php

use Laravel\Mcp\Facades\Mcp;
use App\Mcp\Servers\RoadmapServer;

Mcp::web('/mcp', RoadmapServer::class)->middleware(['auth:sanctum', 'throttle:api']);
