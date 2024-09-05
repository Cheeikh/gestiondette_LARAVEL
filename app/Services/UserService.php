<?php

namespace App\Services;

use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\UserServiceInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService implements UserServiceInterface
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function registerUser(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        return $this->userRepository->create($data);  // Utilisation de -> au lieu de .
    }

    public function getAllUsers(): array
    {
        return $this->userRepository->getAll();  // Utilisation de -> au lieu de .
    }

    public function getUsersByRole(string $role): array
    {
        return $this->userRepository->getByRole($role);  // Utilisation de -> au lieu de .
    }

    public function getUsersByRoleAndActive(string $role, bool $active): array
    {
        return $this->userRepository->getByRoleAndActive($role, $active);  // Utilisation de -> au lieu de .
    }
}
