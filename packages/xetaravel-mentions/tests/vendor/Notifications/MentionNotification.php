<?php
namespace Tests\vendor\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MentionNotification extends Notification
{
    use Queueable;

    /**
     * The Model instance.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    public $model;

    /**
     * Create a new notification instance.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function toDatabase($notifiable): array
    {
        $username = $this->model->user->username;
        $modelId = $this->model->getKey();

        $message = "<strong>@{$username}</strong> has mentionned your name in his article !";
        $link = "/articles/show/{$modelId}";

        return [
            'message' => $message,
            'link' => $link,
            'type' => 'mention'
        ];
    }
}
