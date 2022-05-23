<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Models\Item;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

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

        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('You got mentioned in item ' . $this->comment->item->title)
            ->line('You got mentioned in the item ' . $this->comment->item->title . ' by ' . $this->comment->user->name . '.')
            ->action('View item', route('items.show', $this->comment->item) . '#comment-' . $this->comment->id)
            ->line('If you do not want to receive notifications like this anymore, you can unsubscribe from your profile.');
    }
}
