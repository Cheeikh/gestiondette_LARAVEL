<?php

namespace App\Interfaces;

use App\Models\Dette;

interface DetteServiceInterface
{
    public function createDette(array $data): Dette;
}
