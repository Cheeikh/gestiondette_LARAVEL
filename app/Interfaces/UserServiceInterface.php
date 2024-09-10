<?php

namespace App\Interfaces;

use App\Models\User;

interface UserServiceInterface
{
    public function registerUser(array $data): User;
    public function getAllUsers(): array;
    public function getUsersByFilters($role, $active): array;
    public function login(array $credentials): array;

}
