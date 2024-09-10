<?php

namespace App\Repositories;

use App\Interfaces\PaiementRepositoryInterface;
use App\Models\Paiement;

class PaiementRepository implements PaiementRepositoryInterface
{
    public function create(array $data): Paiement
    {
        return Paiement::create($data);
    }
}
