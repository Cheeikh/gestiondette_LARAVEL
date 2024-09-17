<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CustomMessageNotification extends Notification
{
    use Queueable;

    protected $messageContent;

    public function __construct($message)
    {
        $this->messageContent = $message;
    }

    public function via($notifiable)
    {
        return ['database', 'sms'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => $this->messageContent,
        ];
    }

    public function toSms($notifiable)
    {
        return $this->messageContent;
    }
}
