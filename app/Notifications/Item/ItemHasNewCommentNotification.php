<?php

namespace App\Notifications\Item;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ItemHasNewCommentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Comment $comment)
    {
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New comment on roadmap item ' . $this->comment->item->title)
            ->line('There is a new comment on a item you\'re subscribed too.')
            ->action('View comment', route('items.show', $this->comment->item) . '#comment-' . $this->comment->id)
            ->line('If you don\'t want these notifications anymore, you can unsubscribe from the item (but still keep the vote) via the unsubscribe button next to the upvote button.');
    }
}
