<?php

namespace App\Repositories;

use App\Interfaces\ClientRepositoryInterface;
use App\Models\Client;
use App\Exceptions\ClientNotFoundException;
use App\Exceptions\ClientCreationException;

class ClientRepository implements ClientRepositoryInterface
{
    public function create(array $data): Client
    {
        try {
            $client = Client::create($data);
            if (!$client) {
                throw new ClientCreationException("Échec de la création du client dans la base de données.");
            }
            return $client;
        } catch (\Exception $e) {
            throw new ClientCreationException("Erreur lors de la création du client : " . $e->getMessage());
        }
    }

    public function findById(int $id): Client
    {
        $client = Client::find($id);
        if (!$client) {
            throw new ClientNotFoundException("Client avec l'ID $id non trouvé.");
        }
        return $client;
    }

    public function getAll(?bool $hasUser = null, ?bool $active = null): array
    {
        $query = Client::query();

        if (!is_null($hasUser)) {
            if ($hasUser) {
                $query->whereHas('user');
            } else {
                $query->doesntHave('user');
            }
        }

        if (!is_null($active)) {
            $query->where('active', $active);
        }

        return $query->get()->toArray();
    }

    public function findClientWithUser(int $id): Client
    {
        $client = Client::with('user')->find($id);
        if (!$client) {
            throw new ClientNotFoundException("Client avec l'ID $id non trouvé.");
        }
        return $client;
    }
}