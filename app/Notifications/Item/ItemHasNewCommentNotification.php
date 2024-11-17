<?php

namespace App\Notifications\Item;

use App\Models\User;
use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\URL;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ItemHasNewCommentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 5;

    public int $backoff = 10;

    public function __construct(
        public readonly Comment $comment,
        public readonly User $user
    ) {
    }

    /**
     * @return array|string[]
     */
    public function via(): array
    {
        if ($this->comment->private) {
            return [];
        }

        return ['mail'];
    }

    public function toMail(User $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(trans('notifications.new-comment-subject', ['title' => $this->comment->item?->title ?? '']))
            ->markdown('emails.item.new-comment', [
                'comment' => $this->comment,
                'user'    => $this->user,
                'url'     => route('items.show', $this->comment->item) . '#comment-' . $this->comment->id,
                'unsubscribeUrl' => URL::signedRoute('items.email-unsubscribe', [
                    'item' => $this->comment->item,
                    'user' => $this->user,
                ]),
            ]);
    }
}
