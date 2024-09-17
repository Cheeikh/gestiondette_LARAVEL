<?php

namespace App\Services;

use App\Interfaces\AuthentificationServiceInterface;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Passport;

class PassportAuthenticationService implements AuthentificationServiceInterface
{
    public function login(array $credentials): array
    {
        if (!Auth::attempt($credentials)) {
            return [
                'status' => 401,
                'message' => 'Unauthorized'
            ];
        }

        $user = Auth::user();

        // Génération du token d'accès (Bearer Token)
        $tokenResult = $user->createToken('API Token');
        $accessToken = $tokenResult->accessToken;
        $refreshToken = $tokenResult->token->id;


        $userData = [
            'id' => $user->id,
            'email' => $user->email,
            'name' => $user->nom,
            'role' => $user->role->name,
        ];

        return [
            'status' => 200,
            'data' => [
                'user' => $userData,
                'access_token' => $accessToken,
                'token_type' => 'Bearer',
                'expires_in' => $tokenResult->token->expires_at->diffInSeconds(now()),
                'refresh_token' => $refreshToken,
            ],
            'message' => 'Login successful'
        ];
    }

    public function register(array $data): array
    {
        // Création de l'utilisateur
        $user = \App\Models\User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        // Génération du token d'accès (Bearer Token)
        $tokenResult = $user->createToken('API Token');
        $accessToken = $tokenResult->accessToken;
        $refreshToken = $tokenResult->token->id;  // ID du token à utiliser pour rafraîchir

        return [
            'status' => 201,
            'data' => [
                'user' => $user,
                'access_token' => $accessToken,
                'token_type' => 'Bearer',
                'expires_in' => $tokenResult->token->expires_at->diffInSeconds(now()),
                'refresh_token' => $refreshToken,
            ],
            'message' => 'User registered successfully'
        ];
    }

    public function logout(): array
    {
        $user = Auth::user();

        // Révocation des tokens
        $user->tokens->each(function ($token) {
            $token->revoke();
        });

        return [
            'status' => 200,
            'message' => 'Logged out successfully'
        ];
    }
}
