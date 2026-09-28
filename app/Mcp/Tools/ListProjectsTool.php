<?php

namespace App\Mcp\Tools;

use App\Models\Board;
use App\Models\Project;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Description;
use App\Mcp\Tools\Concerns\InteractsWithRoadmap;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;

#[Name('list-projects')]
#[Description('List all roadmap projects you have access to, including the boards (statuses) of each project.')]
#[IsReadOnly]
#[IsIdempotent]
class ListProjectsTool extends Tool
{
    use InteractsWithRoadmap;

    public function handle(Request $request): Response
    {
        $showHiddenBoards = (bool) $this->currentUser()?->hasAdminAccess();

        $projects = Project::query()
            ->visibleForCurrentUser()
            ->with(['boards' => fn ($query) => $query->when(! $showHiddenBoards, fn ($query) => $query->visible())])
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        return Response::json([
            'projects' => $projects->map(fn (Project $project) => [
                ...$this->presentProject($project),
                'boards' => $project->boards->map(fn (Board $board) => $this->presentBoard($board))->values()->all(),
            ])->values()->all(),
        ]);
    }
}
