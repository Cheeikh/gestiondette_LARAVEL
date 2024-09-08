<?php

namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use App\Exceptions\UserNotFoundException;
use App\Exceptions\UserCreationException;
use Illuminate\Database\QueryException;

class UserRepository implements UserRepositoryInterface
{
    public function create(array $data): User
    {
        try {
            return User::create($data);
        } catch (QueryException $e) {
            throw new UserCreationException("Erreur lors de la création de l'utilisateur : " . $e->getMessage());
        }
    }

    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    public function findByLogin(string $login): ?User
    {
        return User::where('login', $login)->first();
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
