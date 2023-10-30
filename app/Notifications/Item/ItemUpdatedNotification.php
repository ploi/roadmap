<?php

namespace App\Notifications\Item;

use App\Models\Item;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\URL;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ItemUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Item $item
    ) {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail(User $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(trans('notifications.item-updated-subject', ['title' => $this->item->title]))
            ->markdown('emails.item.updated', [
                'user'       => $notifiable,
                'item'       => $this->item,
                'activities' => $this->item->activities()->latest()->limit(2)->get(),
                'unsubscribeUrl' => URL::signedRoute('items.email-unsubscribe', [
                    'item' => $this->item,
                    'user' => $notifiable,
                ]),
            ]);
    }
}
