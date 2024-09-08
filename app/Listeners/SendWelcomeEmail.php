<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmail
{

    public function handle(UserCreated $event): void
    {
        // Envoyer un e-mail de bienvenue à l'utilisateur
        $user = $event->user;

        Mail::to($user->email)->send(new WelcomeMail($user));
    }
}
