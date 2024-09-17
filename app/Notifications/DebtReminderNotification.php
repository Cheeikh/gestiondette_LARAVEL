<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Models\Client;

class DebtReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $clientId;
    protected $surname;
    protected $totalDue;

    public function __construct($client, $totalDue)
    {
        $this->clientId = $client->id;
        $this->surname = $client->surname;
        $this->totalDue = $totalDue;
    }

    public function via($notifiable)
    {
        return ['database', 'sms'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'client_id' => $this->clientId,
            'total_due' => $this->totalDue,
            'message' => "Vous avez un montant total impayé de {$this->totalDue}."
        ];
    }

    public function toSms($notifiable)
    {
        return "Cher {$this->surname}, vous avez un montant total impayé de {$this->totalDue}. Veuillez régler votre dette dès que possible.";
    }
}
