<?php

namespace App\Interfaces;

use App\Models\User;

interface UserServiceInterface
{
    public function registerUser(array $data): User;
    public function getAllUsers(): array;
    public function getUsersByRole(string $role): array;
    public function getUsersByRoleAndActive(string $role, bool $active): array;
}
