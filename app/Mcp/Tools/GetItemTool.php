<?php

namespace App\Mcp\Tools;

use App\Models\Comment;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Description;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use App\Mcp\Tools\Concerns\InteractsWithRoadmap;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;

#[Name('get-item')]
#[Description('Get a single roadmap item with its full (markdown) content and comments. Replies reference their parent comment through "parent_id".')]
#[IsReadOnly]
#[IsIdempotent]
class GetItemTool extends Tool
{
    use InteractsWithRoadmap;

    public function handle(Request $request): Response
    {
        $request->validate([
            'item' => ['required'],
        ]);

        $item = $this->findItem($request->get('item'));

        if (! $item) {
            return Response::error('Item not found.');
        }

        $item->load(['project', 'board', 'tags', 'user:id,name,username', 'assignedUsers:id,name,username']);

        $comments = $item->comments()
            ->with('user:id,name,username')
            ->when(! $this->currentUser()?->hasAdminAccess(), fn ($query) => $query->where('private', false))
            ->oldest()
            ->oldest('id')
            ->get();

        return Response::json([
            ...$this->presentItem($item),
            'content' => $item->content,
            'author' => $item->user?->name,
            'author_username' => $item->user?->username,
            'assigned_users' => $item->assignedUsers->pluck('name')->values()->all(),
            'comments_blocked' => (bool) $item->board?->block_comments,
            'updated_at' => $item->updated_at?->toIso8601String(),
            'comments' => $comments->map(fn (Comment $comment) => $this->presentComment($comment))->all(),
        ]);
    }

    /**
     * @return array<string, \Illuminate\JsonSchema\Types\Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'item' => $schema->string()->description('The ID or slug of the item.')->required(),
        ];
    }
}
