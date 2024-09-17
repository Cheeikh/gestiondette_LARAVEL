<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Interfaces\SmsServiceInterface;

class DemandeAnnuleeNotification extends Notification
{
    use Queueable;

    protected $demande;
    protected $motif;

    public function __construct($demande, $motif = null)
    {
        $this->demande = $demande;
        $this->motif = $motif;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'demande_id' => $this->demande->id,
            'title' => 'Votre demande a été annulée',
            'message' => 'Votre demande a été annulée.',
            'motif' => $this->motif,
        ];
    }

    public function toSms()
    {
        $smsService = app(SmsServiceInterface::class);

        $client = $this->demande->client;
        $telephone = $client->telephone;

        $message = "Cher {$client->user->name}, votre demande #{$this->demande->id} a été annulée.";

        if ($this->motif) {
            $message .= " Motif : {$this->motif}";
        }

        $smsService->sendSms($telephone, $message);
    }
}
