<?php

namespace App\Services;

use App\Interfaces\ArchiveServiceInterface;
use App\Interfaces\DetteRepositoryInterface;
use App\Interfaces\DetteServiceInterface;
use Exception;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Log;

class ArchiveFirebaseService implements ArchiveServiceInterface
{
    protected $detteRepository;
    protected $detteService;
    protected $firebase;

    public function __construct(DetteRepositoryInterface $detteRepository, DetteServiceInterface $detteService)
    {
        $this->detteRepository = $detteRepository;
        $this->detteService = $detteService;
        $this->firebase = (new Factory)
            ->withServiceAccount("/home/csm/gestion_dette1/firebase_credentials.json")
            ->withDatabaseUri("https://projet-laravel-88216-default-rtdb.firebaseio.com")
            ->createDatabase();
    }

    public function archivePaidDebts()
    {
        $dettes = $this->detteRepository->getPaidDebts();
        $database = $this->firebase;

        foreach ($dettes as $dette) {
            $paiements = $dette->paiements->map(function ($paiement) {
                return [
                    'montant' => $paiement->montant,
                    'date' => $paiement->date
                ];
            })->toArray();

            $articles = $dette->articles->map(function ($article) {
                return [
                    'articleId' => $article->id,
                    'libelle' => $article->libelle,
                    'prix' => $article->prix,
                    'qteVente' => $article->pivot->qte_vente,
                    'prixVente' => $article->pivot->prix_vente
                ];
            })->toArray();

            try {
                $database->getReference('archives')
                    ->push([
                        'date_archived' => now()->toDateTimeString(),  // Utilisation de toDateTimeString pour plus de précision
                        'client' => [
                            'id' => $dette->client->id,
                            'surname' => $dette->client->surname,
                            'telephone' => $dette->client->telephone,
                            'email' => $dette->client->email,
                            'adresse' => $dette->client->adresse,
                            'dette' => [
                                'debt_id' => $dette->id,
                                'montant' => $dette->montant,
                                'date' => $dette->date,
                                'paiements' => $paiements,
                                'articles' => $articles,
                            ],
                        ]
                    ]);

                $dette->delete();
                Log::info("Dette ID {$dette->id} archivée avec succès dans Firebase.");
            } catch (Exception $e) {
                Log::error("Erreur lors de l'archivage de la dette ID {$dette->id} dans Firebase: " . $e->getMessage());
            }
        }
    }

    public function getArchivedDebts($filter = [])
    {
        $debtsRef = $this->firebase->getReference('archives');
        $snapshot = $debtsRef->getSnapshot()->getValue();

        if (empty($snapshot)) {
            return [];
        }

        $filteredDebts = [];

        foreach ($snapshot as $debtKey => $debt) {
            $includeDebt = true;

            if (isset($filter['client_id']) && $debt['client']['id'] != $filter['client_id']) {
                $includeDebt = false;
            }

            if ($includeDebt && isset($filter['date']) && $debt['date_archived'] != $filter['date']) {
                $includeDebt = false;
            }

            if ($includeDebt) {
                $filteredDebts[$debtKey] = $debt;
            }
        }

        return $filteredDebts;
    }

    public function getArchivedDebtsByClient($filter)
    {
        return $this->getArchivedDebts($filter);
    }

    public function restoreDebtsByDate($date)
    {
        $archivesRef = $this->firebase->getReference('archives');
        $archivedDettes = $archivesRef
            ->orderByChild('date_archived')
            ->equalTo($date)
            ->getSnapshot()
            ->getValue();

        if (empty($archivedDettes)) {
            Log::info("Aucune dette archivée trouvée pour la date $date");
            return false;
        }

        $this->restoreArchives($archivedDettes);

        return true;
    }

    public function restoreDebtById($id)
    {
        $allArchives = $this->firebase->getReference('archives')
            ->getSnapshot()
            ->getValue();

        $found = false;

        if (!empty($allArchives)) {
            foreach ($allArchives as $key => $archive) {
                $debtId = $archive['client']['dette']['debt_id'] ?? null;
                if ($debtId == $id) {
                    $found = true;
                    $this->restoreArchive($key, $archive);
                    break;
                }
            }
        }

        if (!$found) {
            Log::error("Dette archivée non trouvée pour ID $id");
            return false;
        }

        return true;
    }

    public function restoreDebtsByClientId($client_id)
    {
        $allArchives = $this->firebase->getReference('archives')
            ->getSnapshot()
            ->getValue();
        $found = false;

        if (!empty($allArchives)) {
            foreach ($allArchives as $key => $archive) {
                if (isset($archive['client']['id']) && $archive['client']['id'] == $client_id) {
                    $found = true;
                    $this->restoreArchive($key, $archive);
                }
            }
        }

        if (!$found) {
            Log::error("Aucune dette archivée trouvée pour le client ID $client_id");
            return false;
        }

        return true;
    }

    private function restoreArchives(array $archives)
    {
        foreach ($archives as $key => $archive) {
            $this->restoreArchive($key, $archive);
        }
    }

    private function restoreArchive($key, $archive)
    {
        try {
            $this->createDetteFromData($archive);
            // Supprimer l'archive après restauration
            $this->firebase->getReference('archives/' . $key)->remove();
            Log::info("Dette archivée avec la clé $key restaurée et supprimée.");
        } catch (Exception $e) {
            Log::error("Erreur lors de la restauration de l'archive avec la clé $key: " . $e->getMessage());
        }
    }

    public function createDetteFromData($archivedDette)
    {
        $detteData = [
            'clientId' => $archivedDette['client']['id'],
            'montant' => $archivedDette['client']['dette']['montant'],
            'date' => $archivedDette['client']['dette']['date'],
            'articles' => $archivedDette['client']['dette']['articles'],
            'paiement' => $archivedDette['client']['dette']['paiements'],
        ];

        try {
            $this->detteService->createDette($detteData);
            Log::info("Dette recréée avec succès pour le client ID {$detteData['clientId']}");
        } catch (Exception $e) {
            Log::error("Erreur lors de la création de la dette pour le client ID {$detteData['clientId']}: " . $e->getMessage());
            throw $e; // Rethrow pour gérer l'exception dans la méthode appelante
        }
    }
}
