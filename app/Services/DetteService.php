<?php

namespace App\Services;

use App\Interfaces\DetteRepositoryInterface;
use App\Interfaces\ArticleRepositoryInterface;
use App\Interfaces\PaiementRepositoryInterface;
use App\Interfaces\DetteServiceInterface;
use App\Models\Dette;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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
            $data['date_echeance'] = $data['date_echeance'] ?? now()->addMonths(1);  // Default to one month from today if not specified

            $dette = $this->detteRepository->create([
                'client_id' => $data['clientId'],
                'montant' => $montantTotal,
                'date' => now(),
                'date_echeance' => $data['date_echeance'],
            ]);

            $this->attachArticlesToDette($dette->id, $data['articles']);

            if (isset($data['paiement']) && is_array($data['paiement'])) {
                foreach ($data['paiement'] as $paiementData) {
                    if (!empty($paiementData) && isset($paiementData['montant'])) {
                        $this->paiementRepository->create([
                            'dette_id' => $dette->id,
                            'montant' => $paiementData['montant'],
                            'date' => now(),  // Considérer la date du paiement si nécessaire
                        ]);
                    }
                }
            }

            return $dette;
        });
    }

    public function attachArticlesToDette(int $detteId, array $articles): void
    {
        foreach ($articles as $articleData) {
            // Vérifier le stock disponible
            $article = $this->articleRepository->findById($articleData['articleId']);
            $stockDisponible = $article->qteStock - $article->quantite_seuil;

            if ($stockDisponible < $articleData['qteVente']) {
                throw new \Exception("Stock insuffisant pour l'article {$article->libelle}");
            }

            // Mettre à jour le stock
            $this->articleRepository->updateStock($articleData['articleId'], $articleData['qteVente']);

            // Attacher l'article à la dette
            $this->detteRepository->attachArticle($detteId, $articleData);
        }
    }

    public function getDettesByStatut(?string $statut): array
    {
        if ($statut === 'Solde') {
            return $this->detteRepository->findByStatut(true);
        } elseif ($statut === 'NonSolde') {
            return $this->detteRepository->findByStatut(false);
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
            $dette = $this->detteRepository->findById($detteId);
            $paiementTotal = $dette->paiements->sum('montant');

            if (($paiementTotal + $data['montant']) > $dette->montant) {
                throw new \Exception('Le montant du paiement dépasse la dette totale.');
            }

            return $this->paiementRepository->create([
                'dette_id' => $detteId,
                'montant' => $data['montant'],
                'date' => now(),
            ]);
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
