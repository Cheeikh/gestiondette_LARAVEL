<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDemandeRequest;
use App\Http\Requests\UpdateDemandeRequest;
use App\Models\Demande;
use App\Interfaces\DemandeServiceInterface;
use App\Notifications\DemandeCreatedNotification;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DemandeController extends Controller
{
    protected $demandeService;

    public function __construct(DemandeServiceInterface $demandeService)
    {
        $this->demandeService = $demandeService;
    }

    public function store(CreateDemandeRequest $request)
    {
        $user = $request->user();
        if ($user->role_id !== 3) {
            return response()->json(['message' => 'Non autorisé'], 401);
        }

        $data = $request->validated();

        // Récupérer le total_amount calculé
        $totalAmount = $request->total_amount;

        // Appeler le service en passant le total_amount
        $demande = $this->demandeService->createDemande($data, $user, $totalAmount);

        return response()->json(['message' => 'Demande créée avec succès', 'demande' => $demande]);
    }


    public function getAllDemandes(Request $request)
    {
        $user = $request->user();

        try {
            $demandes = $this->demandeService->getDemandes($user, $request->all());
            return response()->json($demandes);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la récupération des demandes', 'error' => $e->getMessage()], 500);
        }
    }

    public function sendRelance($id)
    {
        $user = Auth::user();

        if ($user->role_id !== 3) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        try {
            $demande = Demande::findOrFail($id);

            $demande = $this->demandeService->sendRelance($demande, $user);
            return response()->json(['message' => 'Demande relancée avec succès', 'demande' => $demande], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Demande non trouvée'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }
    }


    public function checkDisponibilite($id)
    {
        $user = Auth::user();

        try {
            $demande = Demande::findOrFail($id);

            $result = $this->demandeService->checkDisponibilite($demande, $user);
            return response()->json($result, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Demande non trouvée'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        }
    }


    public function update(Request $request, $id)
    {
        $user = $request->user();

        $data = $request->all();

        $demande = Demande::findOrFail($id);

        try {
            $demande = $this->demandeService->updateDemandeStatus($demande, $data, $user);
            return response()->json(['message' => 'Demande mise à jour avec succès', 'demande' => $demande]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Demande non trouvée'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la mise à jour de la demande', 'error' => $e->getMessage()], 500);
        }
    }


    public function getClientNotifications(Request $request)
    {
        $user = $request->user();

        try {
            $notifications = $this->demandeService->getClientNotifications($user);
            return response()->json($notifications);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        }
    }

    public function getClientDemandes(Request $request)
    {
        $user = $request->user();

        if ($user->role_id !== 3) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $filters = $request->only('status');

        try {
            $demandes = $this->demandeService->getDemandes($user, $filters);
            return response()->json(['demandes' => $demandes], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la récupération des demandes', 'error' => $e->getMessage()], 500);
        }
    }

    public function getNotifications(Request $request)
    {
        $user = $request->user();

        // Vérifier si l'utilisateur a bien le role_id 2 (Admin ou Responsable)
        if ($user->role_id !== 2) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            // Récupérer les notifications liées aux demandes
            $notifications = $user->notifications()
                ->whereIn('type', [
                    DemandeCreatedNotification::class
                ])
                ->get();

            return response()->json(['notifications' => $notifications], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la récupération des notifications', 'error' => $e->getMessage()], 500);
        }
    }


}
