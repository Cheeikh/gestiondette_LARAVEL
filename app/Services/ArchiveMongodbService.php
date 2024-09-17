<?php

namespace App\Services;

use App\Interfaces\ArchiveServiceInterface;
use App\Interfaces\DetteRepositoryInterface;
use App\Interfaces\DetteServiceInterface;
use App\Models\ArchivedDette;
use Exception;
use Illuminate\Support\Facades\Log;

class ArchiveMongodbService implements ArchiveServiceInterface
{
    protected $detteRepository;
    protected $detteService;

    public function __construct(DetteRepositoryInterface $detteRepository, DetteServiceInterface $detteService)
    {
        $this->detteRepository = $detteRepository;
        $this->detteService = $detteService;
    }

    public function archivePaidDebts()
    {
        // Récupération des dettes payées
        $dettes = $this->detteRepository->getPaidDebts();

        foreach ($dettes as $dette) {
            try {
                // Archivage de la dette
                ArchivedDette::create([
                    'date_archived' => now()->toDateTimeString(),
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
                            'paiements' => $dette->paiements->map(function ($paiement) {
                                return [
                                    'montant' => $paiement->montant,
                                    'date' => $paiement->date,
                                ];
                            })->toArray(),
                            'articles' => $dette->articles->map(function ($article) {
                                return [
                                    'articleId' => $article->id,
                                    'libelle' => $article->libelle,
                                    'prix' => $article->prix,
                                    'qteVente' => $article->pivot->qte_vente,
                                    'prixVente' => $article->pivot->prix_vente,
                                ];
                            })->toArray(),
                        ],
                    ],
                ]);

                // Suppression de la dette après archivage réussi
                $dette->delete();
                Log::info("Dette ID {$dette->id} archivée avec succès dans MongoDB.");
            } catch (Exception $e) {
                Log::error("Erreur lors de l'archivage de la dette ID {$dette->id} dans MongoDB: " . $e->getMessage());
            }
        }
    }

    public function getArchivedDebts($filter = [])
    {
        $query = ArchivedDette::query();

        if (isset($filter['client_id'])) {
            $query->where('client.id', (int)$filter['client_id']);
        }
        if (isset($filter['date'])) {
            $query->where('date_archived', $filter['date']);
        }

        $archivedDettes = $query->get();

        return $archivedDettes->toArray();
    }

    public function getArchivedDebtsByClient($filter)
    {
        return $this->getArchivedDebts($filter);
    }

    public function restoreDebtsByDate($date)
    {
        $archivedDettes = ArchivedDette::where('date_archived', $date)->get();

        if ($archivedDettes->isEmpty()) {
            Log::info("Aucune dette archivée trouvée pour la date {$date}");
            return false;
        }

        $errors = [];

        foreach ($archivedDettes as $archivedDette) {
            try {
                $this->createDetteFromData($archivedDette);
            } catch (Exception $e) {
                $errors[] = "Erreur lors de la restauration de la dette archivée ID {$archivedDette->client['dette']['debt_id']}: " . $e->getMessage();
                Log::error(end($errors));
            }
        }

        return empty($errors) ? true : $errors;
    }

    public function restoreDebtById($id)
    {
        $id = (int)$id;
        $archivedDette = ArchivedDette::where('client.dette.debt_id', $id)->first();

        if (!$archivedDette) {
            Log::error("Dette archivée non trouvée pour ID $id");
            return false;
        }

        try {
            $this->createDetteFromData($archivedDette);
            return true;
        } catch (Exception $e) {
            Log::error("Erreur lors de la restauration de la dette archivée ID {$id}: " . $e->getMessage());
            return false;
        }
    }

    public function restoreDebtsByClientId($client_id)
    {
        $client_id = (int)$client_id;

        $archivedDettes = ArchivedDette::where('client.id', $client_id)->get();

        if ($archivedDettes->isEmpty()) {
            Log::info("Aucune dette archivée trouvée pour le client ID {$client_id}");
            return false;
        }

        $errors = [];

        foreach ($archivedDettes as $archivedDette) {
            try {
                $this->createDetteFromData($archivedDette);
            } catch (Exception $e) {
                $errors[] = "Erreur lors de la restauration de la dette archivée ID {$archivedDette->client['dette']['debt_id']}: " . $e->getMessage();
                Log::error(end($errors));
            }
        }

        return empty($errors) ? true : $errors;
    }

    /**
     * Crée une dette à partir des données archivées et supprime l'archive.
     *
     * @param ArchivedDette $archivedDette
     * @return void
     * @throws Exception
     */
    public function createDetteFromData(ArchivedDette $archivedDette)
    {
        $detteData = [
            'clientId' => $archivedDette->client['id'],
            'montant' => $archivedDette->client['dette']['montant'],
            'date' => $archivedDette->client['dette']['date'],
            'articles' => $archivedDette->client['dette']['articles'],
            'paiement' => $archivedDette->client['dette']['paiements'],
        ];

        // Utiliser DetteService pour créer la dette
        $this->detteService->createDette($detteData);

        // Supprimer l'archive après restauration réussie
        $archivedDette->delete();
        Log::info("Archive ID {$archivedDette->_id} supprimée après restauration.");
    }
}
