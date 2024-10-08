<?php

namespace App\Repositories;

use App\Interfaces\DetteRepositoryInterface;
use App\Models\Client;
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
        return Dette::select('dettes.*')
            ->leftJoin('paiements', 'dettes.id', '=', 'paiements.dette_id')
            ->groupBy('dettes.id')
            ->havingRaw('SUM(paiements.montant) ' . ($isSolde ? '>=' : '<') . ' dettes.montant')
            ->get()
            ->toArray();
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

    public function getClientsWithUnpaidDebts()
    {
        return Client::whereHas('dettes', function ($query) {
            $query->whereRaw('dettes.montant > (SELECT COALESCE(SUM(paiements.montant), 0) FROM paiements WHERE paiements.dette_id = dettes.id)');
        })->get();
    }

    public function getPaidDebts()
    {
        // Filtrer les dettes totalement payées
        return Dette::with(['client', 'articles', 'paiements'])
            ->get()
            ->filter(function ($dette) {
                $totalPaiements = $dette->paiements->sum('montant');
                return $totalPaiements >= $dette->montant;
            });
    }

    public function getPaidDebtsIds()
    {
        // Fetch debts that are fully paid and return their IDs
        return Dette::with(['paiements'])
            ->get()
            ->filter(function ($dette) {
                $totalPaiements = $dette->paiements->sum('montant');
                return $totalPaiements >= $dette->montant;
            })
            ->pluck('id');  // Collects only the IDs of these debts
    }

    public function getClientsByIds(array $clientIds)
    {
        return Client::whereIn('id', $clientIds)->with('dettes')->get();
    }


}
