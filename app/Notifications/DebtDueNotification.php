<?php

namespace App\Notifications;

use App\Channels\SmsChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Interfaces\SmsServiceInterface;
use Illuminate\Contracts\Queue\ShouldQueue;

class DebtDueNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $dette;
    public $totalDue;

    public function __construct($dette, $totalDue)
    {
        $this->dette = $dette;
        $this->totalDue = $totalDue;
    }

    public function via($notifiable)
    {
        return ['database', SmsChannel::class];
    }

    public function toDatabase($notifiable)
    {
        return [
            'client_id' => $this->dette->client->id,
            'debt_id' => $this->dette->id,
            'total_due' => $this->totalDue,
            'message' => "You have an outstanding total payment of {$this->totalDue} due for debt ID {$this->dette->id}."
        ];
    }

    public function toSms($notifiable)
    {
        $smsService = app(SmsServiceInterface::class);

        $message = "Dear {$this->dette->client->surname}, you have an outstanding total payment of {$this->totalDue} for debt ID {$this->dette->id} that you have to pay since {$this->dette->date_echeance}. Please pay your debt as soon as possible.";
        $smsService->sendSms($this->dette->client->telephone, $message);
    }
}
