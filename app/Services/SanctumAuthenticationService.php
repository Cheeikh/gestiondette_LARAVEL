<?php

namespace App\Services;

use App\Interfaces\AuthentificationServiceInterface;
use Illuminate\Support\Facades\Auth;

class SanctumAuthenticationService implements AuthentificationServiceInterface
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
        $token = $user->createToken('API Token')->plainTextToken;

        return [
            'status' => 200,
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
            'message' => 'Login successful'
        ];
    }
}
