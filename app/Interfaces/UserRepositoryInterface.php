<?php

namespace App\Interfaces;

use App\Models\User;

interface UserRepositoryInterface
{
    public function create(array $data): User;
    public function findById(int $id): ?User;
    public function findByLogin(string $login): ?User;
    public function getAll(): array;
    public function getByRole(string $role): array;
    public function getByRoleAndActive(string $role, bool $active): array;
}
