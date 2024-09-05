<?php

namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use App\Exceptions\UserNotFoundException;
use App\Exceptions\UserCreationException;

class UserRepository implements UserRepositoryInterface
{
    public function create(array $data): User
    {
        try {
            $user = User::create($data);
            if (!$user) {
                throw new UserCreationException("Échec de la création de l'utilisateur dans la base de données.");
            }
            return $user;
        } catch (\Exception $e) {
            throw new UserCreationException("Erreur lors de la création de l'utilisateur : " . $e->getMessage());
        }
    }

    public function findById(int $id): User
    {
        $user = User::find($id);
        if (!$user) {
            throw new UserNotFoundException("Utilisateur avec l'ID $id non trouvé.");
        }
        return $user;
    }

    public function findByLogin(string $login): User
    {
        $user = User::where('login', $login)->first();
        if (!$user) {
            throw new UserNotFoundException("Utilisateur avec le login '$login' non trouvé.");
        }
        return $user;
    }

    public function getAll(): array
    {
        return User::all()->toArray();
    }

    public function getByRole(string $role): array
    {
        return User::whereHas('role', function ($query) use ($role) {
            $query->where('name', $role);
        })->get()->toArray();
    }

    public function getByRoleAndActive(string $role, bool $active): array
    {
        return User::whereHas('role', function ($query) use ($role) {
            $query->where('name', $role);
        })->where('active', $active)->get()->toArray();
    }
}