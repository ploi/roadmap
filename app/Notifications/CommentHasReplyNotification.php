<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CommentHasReplyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Comment $comment)
    {
    }

    public function via($notifiable)
    {
        if (!$notifiable->wantsNotification('receive_comment_reply_notifications')) {
            return [];
        }

        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New reply on your comment on roadmap item ' . $this->comment->item->title)
            ->line('There is a new reply on a comment you posted.')
            ->action('View comment', route('items.show', $this->comment->item) . '#comment-' . $this->comment->id)
            ->line('If you do not want to receive notifications like this anymore, you can unsubscribe from your profile.');
    }
}
