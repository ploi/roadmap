<?php

namespace App\Notifications\Item;

use App\Models\User;
use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ItemHasNewCommentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Comment $comment,
        public readonly User $user
    ) {
    }

    public function via($notifiable): array
    {
        if ($this->comment->private) {
            return [];
        }

        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(trans('notifications.new-comment-subject', ['title' => $this->comment->item->title]))
            ->markdown('emails.item.new-comment', [
                'comment' => $this->comment,
                'user'    => $this->user,
                'url'     => route('items.show', $this->comment->item) . '#comment-' . $this->comment->id,
            ]);
    }
}
