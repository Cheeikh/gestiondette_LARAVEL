<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class DemandeCreatedNotification extends Notification
{
    use Queueable;

    protected $demande;

    public function __construct($demande)
    {
        $this->demande = $demande;
    }

    public function via($notifiable)
    {
        return ['database']; // Only use the database channel
    }

    public function toDatabase()
    {
        return [
            'demande_id' => $this->demande->id,
            'title' => 'New Demande Submitted',
            'message' => 'A new demande has been submitted. Total Amount: ' . $this->demande->total_amount,
            'description' => $this->demande->description
        ];
    }
}
