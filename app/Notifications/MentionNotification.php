<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class MentionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 5;

    public int $backoff = 10;

    public function __construct(
        public readonly Comment $comment
    ) {
    }

    public function via(User $notifiable): array
    {
        if ($this->comment->user?->is($notifiable)) {
            return [];
        }

        if (!$notifiable->wantsNotification('receive_mention_notifications')) {
            return [];
        }

        if ($this->comment->private && !$notifiable->hasAdminAccess()) {
            return [];
        }

        return ['mail'];
    }

    public function toMail(User $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(trans('notifications.new-mention-subject', ['title' => $this->comment->item?->title ?? '']))
            ->line(trans('notifications.new-mention-body', ['title' => $this->comment->item?->title ?? '', 'user' => $this->comment->user?->name ?? '']))
            ->action(trans('notifications.view-item'), route('items.show', $this->comment->item) . '#comment-' . $this->comment->id)
            ->line(trans('notifications.unsubscribe-info'));
    }
}
