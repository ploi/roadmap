<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\URL;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmailChange extends Notification
{
    use Queueable;

    public function __construct(
        public string $pendingEmail
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Verify Your New Email Address')
            ->line('You recently requested to change your email address.')
            ->line('Your new email address will be: ' . $this->pendingEmail)
            ->action('Verify New Email Address', $verificationUrl)
            ->line('This verification link will expire in 60 minutes.')
            ->line('If you did not request this change, please ignore this email or contact support.');
    }

    protected function verificationUrl(object $notifiable): string
    {
        return URL::temporarySignedRoute(
            'profile.verify-email-change',
            now()->addMinutes(60),
            [
                'id' => $notifiable->getKey(),
                'email' => $this->pendingEmail,
            ]
        );
    }

    public function toArray(object $notifiable): array
    {
        return [
            'pending_email' => $this->pendingEmail,
        ];
    }
}
