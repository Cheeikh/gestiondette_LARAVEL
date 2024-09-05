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
        $token = $user->createToken('API Token')->accessToken;

        return [
            'status' => 200,
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
            'message' => 'Login successful'
        ];
    }

    public function register(array $data): array
    {
        // Implémentez la logique d'enregistrement ici
        // Ceci est un exemple, adaptez-le à vos besoins
        $user = \App\Models\User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        $token = $user->createToken('API Token')->accessToken;

        return [
            'status' => 201,
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
            'message' => 'User registered successfully'
        ];
    }

    public function logout(): array
    {
        $user = Auth::user();
        $user->tokens->each(function ($token, $key) {
            $token->delete();
        });

        return [
            'status' => 200,
            'message' => 'Logged out successfully'
        ];
    }
}