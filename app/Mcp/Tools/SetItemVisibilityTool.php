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

#[Name('set-item-visibility')]
#[Description('Make a roadmap item private, so only admins and employees can see it, or make it public again. Only available to admins and employees.')]
#[IsIdempotent]
class SetItemVisibilityTool extends Tool
{
    use InteractsWithRoadmap;

    public function handle(Request $request): Response
    {
        if (! $this->currentUser()?->hasAdminAccess()) {
            return Response::error('You are not allowed to change the visibility of items.');
        }

        $validated = $request->validate([
            'item' => ['required'],
            'private' => ['required', 'boolean'],
        ]);

        $item = $this->findItem($validated['item']);

        if (! $item) {
            return Response::error('Item not found.');
        }

        $private = (bool) $validated['private'];
        $visibility = $private ? 'private' : 'public';

        if ($item->isPrivate() === $private) {
            return Response::text("\"{$item->title}\" is already {$visibility}.");
        }

        $item->update(['private' => $private]);

        return Response::json([
            'message' => "Made \"{$item->title}\" {$visibility}.",
            'item' => $this->presentItem($item->refresh()->load(['project', 'board', 'tags'])),
        ]);
    }

    /**
     * Only admins and employees can change the visibility of items, so there is no need to offer this tool to other users.
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
            'item' => $schema->string()->description('The ID or slug of the item.')->required(),
            'private' => $schema->boolean()->description('True to make the item private, false to make it public.')->required(),
        ];
    }
}
