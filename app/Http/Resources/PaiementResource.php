<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaiementResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'montant' => $this->montant,
            'created_at' => $this->created_at,
        ];
    }
}
