<?php

namespace App\Interfaces;

use App\Models\Client;

interface ClientRepositoryInterface
{
    public function create(array $data): Client;
    public function findById(int $id): ?Client;
    public function getAll(?bool $hasUser = null, ?bool $active = null): array;
    public function findClientWithUser(int $id): ?Client;
}
