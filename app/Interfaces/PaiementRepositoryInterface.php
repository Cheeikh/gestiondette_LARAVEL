<?php

namespace App\Interfaces;

use App\Models\Paiement;

interface PaiementRepositoryInterface
{
    public function create(array $data): Paiement;
}
