<?php

namespace App\Http\Controllers;

use App\Facades\DetteFacade;
use App\Http\Requests\DetteRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Interfaces\ArchiveServiceInterface;
use Illuminate\Validation\ValidationException;

class DetteController extends Controller
{
    protected $archiveService;

    public function __construct(ArchiveServiceInterface $archiveService)
    {
        $this->archiveService = $archiveService;
    }

    // Création d'une nouvelle dette
    public function store(DetteRequest $request)
    {
        $dette = DetteFacade::createDette($request->validated());
        return response()->json($dette, Response::HTTP_CREATED);
    }

    // Lister toutes les dettes filtrées par statut (Solde ou NonSolde)
    public function listAll(Request $request)
    {
        $request->validate([
            'statut' => 'nullable|in:Solde,NonSolde',
        ]);

        $statut = $request->query('statut');
        $dettes = DetteFacade::getDettesByStatut($statut);
        return response()->json($dettes, Response::HTTP_OK);
    }

    // Afficher une dette avec le client
    public function show($id)
    {
        // Validation du paramètre $id
        if (!is_numeric($id)) {
            return response()->json(['error' => 'ID invalide'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $dette = DetteFacade::getDetteWithClient((int)$id);
            if (!$dette) {
                return response()->json(['error' => 'Dette non trouvée'], Response::HTTP_NOT_FOUND);
            }
            return response()->json($dette, Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Dette non trouvée'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            // Loggez l'erreur pour le débogage
            \Log::error("Erreur lors de l'affichage de la dette ID $id: " . $e->getMessage());
            return response()->json(['error' => 'Erreur interne du serveur'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Lister les articles d'une dette
    public function listArticles($id)
    {
        if (!is_numeric($id)) {
            return response()->json(['error' => 'ID invalide'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $detteWithArticles = DetteFacade::getDetteWithArticles((int)$id);
            if (!$detteWithArticles) {
                return response()->json(['error' => 'Dette ou articles non trouvés'], Response::HTTP_NOT_FOUND);
            }
            return response()->json(['data' => $detteWithArticles], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Dette non trouvée'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            \Log::error("Erreur lors de la récupération des articles pour la dette ID $id: " . $e->getMessage());
            return response()->json(['error' => 'Erreur interne du serveur'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Ajouter un paiement à une dette
    public function addPaiement(Request $request, $id)
    {
        // Validation du paramètre $id
        if (!is_numeric($id)) {
            return response()->json(['error' => 'ID invalide'], Response::HTTP_BAD_REQUEST);
        }

        $validatedData = $request->validate([
            'montant' => 'required|numeric|min:0',
        ]);

        try {
            $paiement = DetteFacade::addPaiement((int)$id, $validatedData);
            return response()->json($paiement, Response::HTTP_CREATED);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Dette non trouvée pour ce paiement'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            \Log::error("Erreur lors de l'ajout du paiement pour la dette ID $id: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    // Lister les paiements d'une dette
    public function listPaiements($id)
    {
        if (!is_numeric($id)) {
            return response()->json(['error' => 'ID invalide'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $paiements = DetteFacade::getPaiementsByDetteId((int)$id);
            return response()->json($paiements, Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Paiements non trouvés pour cette dette'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            \Log::error("Erreur lors de la récupération des paiements pour la dette ID $id: " . $e->getMessage());
            return response()->json(['error' => 'Erreur interne du serveur'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Afficher les dettes archivées
    public function showArchived(Request $request)
    {
        $filter = $request->validate([
            'client_id' => 'nullable|integer',
            'date' => 'nullable|date',
        ]);

        $archivedDebts = $this->archiveService->getArchivedDebts($filter);
        return response()->json($archivedDebts, Response::HTTP_OK);
    }

    // Afficher les dettes archivées d'un client
    public function showClientArchivedDettes($client_id)
    {
        if (!is_numeric($client_id)) {
            return response()->json(['error' => 'ID client invalide'], Response::HTTP_BAD_REQUEST);
        }

        $archivedDettes = $this->archiveService->getArchivedDebtsByClient(['client_id' => (int)$client_id]);
        return response()->json($archivedDettes, Response::HTTP_OK);
    }

    // Restaurer les dettes par date
    public function restoreDebtsByDate($date)
    {
        // Validation de la date
        if (!strtotime($date)) {
            return response()->json(['error' => 'Date invalide'], Response::HTTP_BAD_REQUEST);
        }

        $result = $this->archiveService->restoreDebtsByDate($date);
        if ($result) {
            return response()->json(['message' => 'Dettes restaurées avec succès.'], Response::HTTP_OK);
        }
        return response()->json(['error' => 'Échec de la restauration des dettes.'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    // Restaurer une dette par ID
    public function restoreDebtById($id)
    {
        if (!is_numeric($id)) {
            return response()->json(['error' => 'ID invalide'], Response::HTTP_BAD_REQUEST);
        }

        $result = $this->archiveService->restoreDebtById((int)$id);
        if ($result) {
            return response()->json(['message' => 'Dette restaurée avec succès.'], Response::HTTP_OK);
        }
        return response()->json(['error' => 'Échec de la restauration de la dette.'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    // Restaurer les dettes d'un client
    public function restoreClientDebts($client_id)
    {
        if (!is_numeric($client_id)) {
            return response()->json(['error' => 'ID client invalide'], Response::HTTP_BAD_REQUEST);
        }

        $result = $this->archiveService->restoreDebtsByClientId((int)$client_id);
        if ($result) {
            return response()->json(['message' => 'Dettes du client restaurées avec succès.'], Response::HTTP_OK);
        }
        return response()->json(['error' => 'Échec de la restauration des dettes du client.'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
