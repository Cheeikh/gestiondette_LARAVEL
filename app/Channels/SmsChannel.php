<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use App\Interfaces\SmsServiceInterface;

class SmsChannel
{
    protected $smsService;

    public function __construct(SmsServiceInterface $smsService)
    {
        $this->smsService = $smsService;
    }

    public function send($notifiable, Notification $notification)
    {
        if (method_exists($notification, 'toSms')) {
            $message = $notification->toSms($notifiable);
            $telephone = $notifiable->telephone;

            $this->smsService->sendSms($telephone, $message);
        }
    }
}
