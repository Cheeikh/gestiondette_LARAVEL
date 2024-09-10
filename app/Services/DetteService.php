<?php

namespace App\Services;

use App\Interfaces\DetteRepositoryInterface;
use App\Interfaces\ArticleRepositoryInterface;
use App\Interfaces\PaiementRepositoryInterface;
use App\Interfaces\DetteServiceInterface;
use App\Models\Dette;
use Illuminate\Support\Facades\DB;

class DetteService implements DetteServiceInterface
{
    protected $detteRepository;
    protected $articleRepository;
    protected $paiementRepository;

    public function __construct(
        DetteRepositoryInterface $detteRepository,
        ArticleRepositoryInterface $articleRepository,
        PaiementRepositoryInterface $paiementRepository
    ) {
        $this->detteRepository = $detteRepository;
        $this->articleRepository = $articleRepository;
        $this->paiementRepository = $paiementRepository;
    }

    // Création d'une nouvelle dette
    public function createDette(array $data): Dette
    {
        return DB::transaction(function () use ($data) {

            $montantTotal = 0;
            foreach ($data['articles'] as $articleData) {
                $montantTotal += $articleData['qteVente'] * $articleData['prixVente'];
            }

            $dette = $this->detteRepository->create([
                'client_id' => $data['clientId'],
                'montant' => $montantTotal,
                'date' => now()
            ]);

            foreach ($data['articles'] as $articleData) {
                $this->articleRepository->updateStock($articleData['articleId'], $articleData['qteVente']);
                $this->detteRepository->attachArticle($dette->id, $articleData);
            }

            if (isset($data['paiement'])) {
                $this->paiementRepository->create([
                    'dette_id' => $dette->id,
                    'montant' => $data['paiement']['montant'],
                    'date' => now()
                ]);
            }

            return $dette;
        });
    }

    public function getDettesByStatut(?string $statut): array
    {
        if ($statut === 'Solde') {
            return $this->detteRepository->findByStatut(true); // MontantRestant = 0
        } elseif ($statut === 'NonSolde') {
            return $this->detteRepository->findByStatut(false); // MontantRestant > 0
        }
        return $this->detteRepository->findAll();
    }

    // Lister une dette avec le client
    public function getDetteWithClient(int $id): ?Dette
    {
        return $this->detteRepository->findWithClientById($id);
    }

    // Lister les articles d'une dette
    public function getDetteWithArticles(int $id): ?Dette
    {
        return $this->detteRepository->findWithArticlesById($id);
    }

    public function addPaiement(int $detteId, array $data)
    {
        return DB::transaction(function () use ($detteId, $data) {
            $paiement = $this->paiementRepository->create([
                'dette_id' => $detteId,
                'montant' => $data['montant'],
                'date' => now()
            ]);

            $dette = $this->detteRepository->findById($detteId);
            $dette->save();

            return $paiement;
        });
    }
    public function getDettesByClientId(int $clientId): array
    {
        return $this->detteRepository->findByClientIdWithoutDetails($clientId);
    }

    public function getPaiementsByDetteId(int $detteId): array
    {
        $dette = $this->detteRepository->findPaiementById($detteId);
        return $dette->paiements->toArray();  // Retourner les paiements associés à la dette
    }

}
