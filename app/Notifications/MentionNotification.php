<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class MentionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Comment $comment)
    {
    }

    public function via($notifiable)
    {
        if (!$notifiable->wantsNotification('receive_mention_notifications')) {
            return [];
        }

        if (!$notifiable->hasAdminAccess() && $this->comment->private) {
            return [];
        }

        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(trans('notifications.new-mention-subject', ['title' => $this->comment->item->title]))
            ->line(trans('notifications.new-mention-body', ['title' => $this->comment->item->title, 'user' => $this->comment->user->name]))
            ->action(trans('notifications.view-item'), route('items.show', $this->comment->item) . '#comment-' . $this->comment->id)
            ->line(trans('notifications.unsubscribe-info'));
    }
}
