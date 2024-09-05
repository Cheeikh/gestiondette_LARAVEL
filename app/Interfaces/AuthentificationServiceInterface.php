<?php

namespace App\Interfaces;

interface AuthentificationServiceInterface
{
    public function login(array $credentials): array;
    public function register(array $data): array;
    public function logout(): array;
}