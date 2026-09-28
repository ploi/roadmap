<?php

namespace App\Mcp\Tools;

use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use App\Rules\ProfanityCheck;
use App\Settings\GeneralSettings;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Description;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use App\Mcp\Tools\Concerns\InteractsWithRoadmap;

#[Name('comment-on-item')]
#[Description('Post a comment on a roadmap item, or reply to an existing comment. Mention users with @username. Admins and employees can post a private note that is only visible to other admins and employees.')]
class CommentOnItemTool extends Tool
{
    use InteractsWithRoadmap;

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'item' => ['required'],
            'content' => ['required', 'string', 'min:3', new ProfanityCheck()],
            'parent_id' => ['nullable', 'integer'],
            'private' => ['nullable', 'boolean'],
        ]);

        $user = $this->currentUser();

        if (! $user) {
            return Response::error('You need to be logged in to comment.');
        }

        if (app(GeneralSettings::class)->users_must_verify_email && ! $user->hasVerifiedEmail()) {
            return Response::error('You need to verify your email address before you can comment.');
        }

        $item = $this->findItem($validated['item']);

        if (! $item) {
            return Response::error('Item not found.');
        }

        if ($item->board?->block_comments) {
            return Response::error('Comments are disabled for items on this board.');
        }

        $private = (bool) ($validated['private'] ?? false);

        if ($private && ! $user->hasAdminAccess()) {
            return Response::error('Only admins and employees can post private notes.');
        }

        $parent = null;

        if (filled($validated['parent_id'] ?? null)) {
            $parent = $item->comments()
                ->when(! $user->hasAdminAccess(), fn ($query) => $query->where('private', false))
                ->find($validated['parent_id']);

            if (! $parent) {
                return Response::error('The comment you are replying to does not exist on this item.');
            }
        }

        $comment = $item->comments()->create([
            'content' => $validated['content'],
            'parent_id' => $parent?->id,
            'user_id' => $user->id,
            'private' => $private,
        ]);

        return Response::json([
            'message' => $comment->private ? 'Private note added.' : 'Comment added.',
            'comment' => $this->presentComment($comment->refresh()->load('user:id,name,username')),
            'url' => $item->view_url . '#comment-' . $comment->id,
        ]);
    }

    /**
     * @return array<string, \Illuminate\JsonSchema\Types\Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'item' => $schema->string()->description('The ID or slug of the item to comment on.')->required(),
            'content' => $schema->string()->min(3)->description('The comment, markdown is supported.')->required(),
            'parent_id' => $schema->integer()->description('The ID of the comment to reply to.'),
            'private' => $schema->boolean()->description('Post as a private note, only available to admins and employees.'),
        ];
    }
}
