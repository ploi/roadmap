<?php

namespace App\Mcp\Servers;

use Laravel\Mcp\Server;
use App\Mcp\Tools\GetItemTool;
use App\Mcp\Tools\MoveItemTool;
use App\Mcp\Tools\ListItemsTool;
use App\Mcp\Tools\GetProjectTool;
use App\Mcp\Tools\ListProjectsTool;
use App\Mcp\Tools\CommentOnItemTool;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;
use Laravel\Mcp\Server\Attributes\Instructions;

#[Name('Roadmap')]
#[Version('1.0.0')]
#[Instructions(<<<'MARKDOWN'
    This server gives access to a product roadmap. The roadmap is organised in projects, each project has boards
    (statuses such as "Under review", "Planned" or "Done") and items (feature requests, ideas and bugs) live on a board.

    - Use `list-projects` to discover projects and their boards, and `get-project` for the item counts per board.
    - Use `list-items` to browse or search items, and `get-item` to read an item with its comments.
    - Use `comment-on-item` to comment on an item or reply to a comment.
    - Admins and employees can use `move-item` to move an item to another board or project.

    Everything happens as the user that owns the API token, so only the projects and items that user can see are available.
    MARKDOWN)]
class RoadmapServer extends Server
{
    protected array $tools = [
        ListProjectsTool::class,
        GetProjectTool::class,
        ListItemsTool::class,
        GetItemTool::class,
        CommentOnItemTool::class,
        MoveItemTool::class,
    ];

    protected array $resources = [
        //
    ];

    protected array $prompts = [
        //
    ];
}
