<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FitnessActivityNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $message,
        public string $url
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'    => 'fitness_activity',
            'message' => $this->message,
            'url'     => $this->url,
        ];
    }
}