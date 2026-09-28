<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Comment;
use App\Services\MentionParser;
use App\Notifications\CommentHasReplyNotification;
use App\Notifications\Item\ItemHasNewCommentNotification;

class CommentObserver
{
    public function created(Comment $comment)
    {
        $this->processMentions($comment);

        $userIds = $comment->item?->votes()
                ->subscribed()
                ->where('user_id', '!=', auth()->id()) // Don't get the current user, they obviously already know about the new comment
                ->pluck('user_id') ?? collect();

        User::query()->whereIn('id', $userIds->toArray())->get()->each(function (User $user) use ($comment) {
            $user->notify(new ItemHasNewCommentNotification($comment, $user));
        });

        $comment->parent?->user->notify(new CommentHasReplyNotification($comment));
    }

    public function updated(Comment $comment)
    {
        if ($comment->wasChanged('content')) {
            $this->processMentions($comment);
        }
    }

    public function deleting(Comment $comment)
    {
        foreach ($comment->comments as $parentComment) {
            $parentComment->delete();
        }

        $comment->mentions()->delete();
    }

    /**
     * Link mentions in the content and notify users that are mentioned for the first time in this comment.
     */
    private function processMentions(Comment $comment): void
    {
        ['content' => $content, 'users' => $users] = app(MentionParser::class)->parse($comment->content ?? '');

        if ($content !== $comment->content) {
            $comment->updateQuietly(['content' => $content]);
        }

        $users
            ->reject(fn (User $user) => $user->id === $comment->user_id)
            ->each(function (User $user) use ($comment) {
                $mention = $comment->mention($user, notify: false);

                if ($mention->wasRecentlyCreated) {
                    $mention->notify($comment, $user);
                }
            });
    }
}
