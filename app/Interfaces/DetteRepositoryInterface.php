<?php

namespace App\Interfaces;

use App\Models\Dette;

interface DetteRepositoryInterface
{
    public function create(array $data): Dette;
    public function findByClientIdWithoutDetails(int $clientId): array;
    public function attachArticle(int $detteId, array $articleData);

    public function findByStatut(bool $isSolde): array;
    public function findAll(): array;

    public function findWithClientById(int $id): ?Dette;
    public function findWithArticlesById(int $id): ?Dette;

    public function findById(int $id): ?Dette;

    public function getPaidDebts();

}
