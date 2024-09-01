<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'qte_stock' => $this->qte_stock,
            'prix_vente' => $this->prix_vente,
            'pivot' => [
                'qte_vente' => $this->pivot->qte_vente,
                'prix_vente' => $this->pivot->prix_vente,
            ],
        ];
    }
}
