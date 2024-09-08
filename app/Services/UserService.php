<?php

namespace App\Services;

use App\Interfaces\AuthentificationServiceInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\UserServiceInterface;
use App\Models\User;
use App\Events\UserRegistering;


class UserService implements UserServiceInterface
{
    protected UserRepositoryInterface $userRepository;
    protected AuthentificationServiceInterface $authService;

    public function __construct(
        UserRepositoryInterface          $userRepository,
        AuthentificationServiceInterface $authService
    )
    {
        $this->userRepository = $userRepository;
        $this->authService = $authService;
    }

    public function registerUser(array $data, $photo = null): User
    {
        $user = $this->userRepository->create($data);
        event(new UserRegistering($user, $photo));
        return $user;
    }

    public function getAllUsers(): array
    {
        return $this->userRepository->getAll();
    }

    public function getUsersByRole(string $role): array
    {
        return $this->userRepository->getByRole($role);
    }

    public function getUsersByRoleAndActive(string $role, bool $active): array
    {
        return $this->userRepository->getByRoleAndActive($role, $active);
    }

    public function login(array $credentials): array
    {
        return $this->authService->login($credentials);
    }
}
