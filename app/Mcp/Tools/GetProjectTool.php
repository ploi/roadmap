<?php

namespace App\Mcp\Tools;

use App\Models\Item;
use App\Models\Board;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Description;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use App\Mcp\Tools\Concerns\InteractsWithRoadmap;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;

#[Name('get-project')]
#[Description('Get a single roadmap project with its boards and the number of items on each board.')]
#[IsReadOnly]
#[IsIdempotent]
class GetProjectTool extends Tool
{
    use InteractsWithRoadmap;

    public function handle(Request $request): Response
    {
        $request->validate([
            'project' => ['required'],
        ]);

        $project = $this->findProject($request->get('project'));

        if (! $project) {
            return Response::error('Project not found.');
        }

        $boards = $this->currentUser()?->hasAdminAccess() ? $project->boards : $project->boards()->visible()->get();

        $itemCounts = Item::query()
            ->visibleForCurrentUser()
            ->where('items.project_id', $project->id)
            ->selectRaw('board_id, count(*) as aggregate')
            ->groupBy('board_id')
            ->pluck('aggregate', 'board_id');

        return Response::json([
            ...$this->presentProject($project),
            'boards' => $boards->map(fn (Board $board) => [
                ...$this->presentBoard($board),
                'items_count' => (int) $itemCounts->get($board->id, 0),
            ])->values()->all(),
        ]);
    }

    /**
     * @return array<string, \Illuminate\JsonSchema\Types\Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'project' => $schema->string()->description('The ID or slug of the project.')->required(),
        ];
    }
}
