<?php

namespace App\Mcp\Tools;

use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Description;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use App\Mcp\Tools\Concerns\InteractsWithRoadmap;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;

#[Name('move-item')]
#[Description('Move a roadmap item to another board, optionally in another project. Only available to admins and employees.')]
#[IsIdempotent]
class MoveItemTool extends Tool
{
    use InteractsWithRoadmap;

    public function handle(Request $request): Response
    {
        if (! $this->currentUser()?->hasAdminAccess()) {
            return Response::error('You are not allowed to move items.');
        }

        $validated = $request->validate([
            'item' => ['required'],
            'board' => ['required'],
            'project' => ['nullable'],
        ]);

        $item = $this->findItem($validated['item']);

        if (! $item) {
            return Response::error('Item not found.');
        }

        $project = filled($validated['project'] ?? null) ? $this->findProject($validated['project']) : $item->project;

        if (! $project) {
            return Response::error(filled($validated['project'] ?? null)
                ? 'Project not found.'
                : 'This item does not belong to a project yet, pass the project to move it to.');
        }

        $board = $this->findBoard($project, $validated['board']);

        if (! $board) {
            return Response::error("Board not found in project \"{$project->title}\".");
        }

        if ($item->board_id === $board->id && $item->project_id === $project->id) {
            return Response::text("\"{$item->title}\" is already on board \"{$board->title}\" in project \"{$project->title}\".");
        }

        $item->update([
            'project_id' => $project->id,
            'board_id' => $board->id,
        ]);

        return Response::json([
            'message' => "Moved \"{$item->title}\" to board \"{$board->title}\" in project \"{$project->title}\".",
            'item' => $this->presentItem($item->refresh()->load(['project', 'board', 'tags'])),
        ]);
    }

    /**
     * Only admins and employees can move items, so there is no need to offer this tool to other users.
     */
    public function shouldRegister(Request $request): bool
    {
        return (bool) $request->user()?->hasAdminAccess();
    }

    /**
     * @return array<string, \Illuminate\JsonSchema\Types\Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'item' => $schema->string()->description('The ID or slug of the item to move.')->required(),
            'board' => $schema->string()->description('The ID or slug of the board to move the item to.')->required(),
            'project' => $schema->string()->description('The ID or slug of the project the board belongs to, defaults to the current project of the item.'),
        ];
    }
}
