<?php

namespace App\Mail;

use App\Models\Demande;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DemandeCreatedMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $demande;

    public function __construct(Demande $demande)
    {
        $this->demande = $demande;
    }

    public function build()
    {
        return $this->subject('New Demande Submitted')
            ->view('emails.demande_created')
            ->with([
                'demande' => $this->demande
            ]);
    }
}
