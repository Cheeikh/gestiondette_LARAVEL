<?php

namespace App\Listeners;

use App\Events\UserCreated;
use Illuminate\Support\Str;
use Laravel\Passport\TokenRepository;
use Laravel\Passport\RefreshTokenRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMail;

class HandleUserCreated
{
    protected $tokenRepository;
    protected $refreshTokenRepository;

    public function __construct(
        TokenRepository $tokenRepository,
        RefreshTokenRepository $refreshTokenRepository
    ) {
        $this->tokenRepository = $tokenRepository;
        $this->refreshTokenRepository = $refreshTokenRepository;
    }

    public function handle(UserCreated $event)
    {
        $user = $event->user;

        // Générer un jeton d'accès pour l'utilisateur
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        $token->save();

        // Créer un refresh token
        $this->refreshTokenRepository->create([
            'id' => Str::random(40),
            'access_token_id' => $token->id,
            'revoked' => false,
            'expires_at' => Carbon::now()->addDays(30),
        ]);

        // Envoyer l'e-mail de bienvenue
        Mail::to($user->email)->send(new WelcomeMail($user));
    }
}
