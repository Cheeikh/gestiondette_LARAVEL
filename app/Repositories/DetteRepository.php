<?php

namespace App\Repositories;

use App\Interfaces\DetteRepositoryInterface;
use App\Models\Dette;

class DetteRepository implements DetteRepositoryInterface
{
    public function create(array $data): Dette
    {
        return Dette::create($data);
    }

    public function attachArticle(int $detteId, array $articleData)
    {
        $dette = Dette::find($detteId);
        $dette->articles()->attach($articleData['articleId'], [
            'qte_vente' => $articleData['qteVente'],
            'prix_vente' => $articleData['prixVente']
        ]);
    }

    public function findByStatut(bool $isSolde): array
    {
        $dettes = Dette::with('paiements')->get();

        $filteredDettes = $dettes->filter(function ($dette) use ($isSolde) {
            $totalPaiements = $dette->paiements->sum('montant');

            if ($isSolde) {
                return $totalPaiements >= $dette->montant;
            } else {
                return $totalPaiements < $dette->montant;
            }
        });

        return $filteredDettes->toArray();
    }

    public function findAll(): array
    {
        return Dette::all()->toArray();
    }

    public function findWithClientById(int $id): ?Dette
    {
        return Dette::with('client')->findOrFail($id);
    }

    public function findWithArticlesById(int $id): ?Dette
    {
        return Dette::with('articles')->findOrFail($id);
    }

    public function findById(int $id): ?Dette
    {
        return Dette::findOrFail($id);
    }

    public function findPaiementById(int $id): ?Dette
    {
        return Dette::with('paiements')->findOrFail($id);
    }


    public function findByClientIdWithoutDetails(int $clientId): array
    {
        return Dette::where('client_id', $clientId)
            ->select('id', 'montant', 'date')
            ->get()
            ->toArray();
    }
}
