<?php

namespace App\Mcp\Tools;

use App\Models\Item;
use App\Models\User;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use App\Rules\ProfanityCheck;
use App\Settings\GeneralSettings;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Description;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use App\Mcp\Tools\Concerns\InteractsWithRoadmap;

#[Name('create-item')]
#[Description('Create a new roadmap item (feature request, idea or bug), optionally on a board of a project. Available to admins and employees, and to other users when an admin allows it.')]
class CreateItemTool extends Tool
{
    use InteractsWithRoadmap;

    public function handle(Request $request): Response
    {
        $user = $this->currentUser();

        if (! $this->canCreateItems($user)) {
            return Response::error('You are not allowed to create items.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'min:3', 'max:255', new ProfanityCheck()],
            'content' => ['required', 'string', 'min:10', new ProfanityCheck()],
            'project' => ['nullable'],
            'board' => ['nullable'],
        ]);

        $settings = app(GeneralSettings::class);

        if ($settings->users_must_verify_email && ! $user->hasVerifiedEmail()) {
            return Response::error('You need to verify your email address before you can create items.');
        }

        $project = null;

        if (filled($validated['project'] ?? null)) {
            $project = $this->findProject($validated['project']);

            if (! $project) {
                return Response::error('Project not found.');
            }
        }

        $board = null;

        if (filled($validated['board'] ?? null)) {
            if (! $project) {
                return Response::error('Pass the project the board belongs to.');
            }

            $board = $this->findBoard($project, $validated['board']);

            if (! $board) {
                return Response::error("Board not found in project \"{$project->title}\".");
            }

            if (! $user->hasAdminAccess() && ! $board->canUsersCreateItem()) {
                return Response::error("Items can't be created on board \"{$board->title}\".");
            }
        }

        if (! $project && $settings->select_project_when_creating_item && $settings->project_required_when_creating_item) {
            return Response::error('A project is required, pass the project to create the item in.');
        }

        if (! $board && $settings->select_board_when_creating_item && $settings->board_required_when_creating_item) {
            return Response::error('A board is required, pass the board to create the item on.');
        }

        $item = Item::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'project_id' => $project?->id,
            'board_id' => $board?->id,
        ]);

        $item->user()->associate($user)->save();
        $item->toggleUpvote($user);

        return Response::json([
            'message' => "Created \"{$item->title}\".",
            'item' => $this->presentItem($item->refresh()->load(['project', 'board', 'tags'])),
        ]);
    }

    /**
     * Admins and employees can always create items, other users only when an admin allows it in the MCP settings.
     */
    public function shouldRegister(Request $request): bool
    {
        return $this->canCreateItems($request->user());
    }

    /**
     * @return array<string, \Illuminate\JsonSchema\Types\Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'title' => $schema->string()->min(3)->max(255)->description('The title of the item.')->required(),
            'content' => $schema->string()->min(10)->description('The description of the item, markdown is supported.')->required(),
            'project' => $schema->string()->description('The ID or slug of the project to create the item in.'),
            'board' => $schema->string()->description('The ID or slug of the board to create the item on, requires the project.'),
        ];
    }

    private function canCreateItems(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $user->hasAdminAccess() || app(GeneralSettings::class)->mcp_users_can_create_items;
    }
}
