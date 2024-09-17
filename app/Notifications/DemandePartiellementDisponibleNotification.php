<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DemandePartiellementDisponibleNotification extends Notification
{
    use Queueable;

    protected $demande;
    protected $articlesDisponibles;

    public function __construct($demande, $articlesDisponibles)
    {
        $this->demande = $demande;
        $this->articlesDisponibles = $articlesDisponibles;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'demande_id' => $this->demande->id,
            'title' => 'Disponibilité partielle de votre demande',
            'message' => 'Certains articles de votre demande ne sont pas entièrement disponibles.',
            'articles' => $this->articlesDisponibles,
        ];
    }

    public function toSms($notifiable)
    {
        $smsService = app(\App\Interfaces\SmsServiceInterface::class);

        $client = $this->demande->client;
        $telephone = $client->telephone;

        $message = "Cher {$client->user->name}, certains articles de votre demande #{$this->demande->id} ne sont pas entièrement disponibles. Veuillez vérifier votre compte pour plus de détails.";

        $smsService->sendSms($telephone, $message);
    }
}
