<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DetteResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'montant' => $this->montant,
            'client' => new ClientResource($this->whenLoaded('client')),
            'articles' => ArticleResource::collection($this->whenLoaded('articles')),
            'paiements' => PaiementResource::collection($this->whenLoaded('paiements')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
