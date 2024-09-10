<?php

namespace App\Interfaces;

use App\Models\Client;

interface ClientServiceInterface
{
    public function registerClient(array $data): Client;
    public function getAllClients(?string $comptes = null, ?string $active = null): array;
    public function getClientById(int $id): ?Client;
    public function getClientWithUser(int $id): ?Client;
    public function createOrUpdateUserForClient(int $clientId, array $userData): Client;

}
