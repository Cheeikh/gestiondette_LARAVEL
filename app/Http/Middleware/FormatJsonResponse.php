<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FormatJsonResponse
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $response = $next($request);

        if ($response instanceof JsonResponse) {

            $data = $response->getData();

            // Récupérer le code de statut de la réponse
            $statusCode = $response->getStatusCode();

            // Messages par défaut en fonction du code de statut
            $defaultMessages = $this->getDefaultMessageForStatusCode($statusCode);

            // Formater la réponse
            $formattedResponse = [
                'status' => $statusCode,
                'data' => $data->data ?? null,
                'message' => $data->message ?? $defaultMessages,
            ];

            return response()->json($formattedResponse, $statusCode);
        }

        return $response;
    }

    /**
     * Récupérer un message par défaut en fonction du code de statut.
     *
     * @param int $statusCode
     * @return string
     */
    private function getDefaultMessageForStatusCode(int $statusCode): string
    {
        switch ($statusCode) {
            case 200:
                return 'Requête traitée avec succès.';
            case 201:
                return 'Ressource créée avec succès.';
            case 204:
                return 'Aucun contenu.';
            case 400:
                return 'Requête invalide.';
            case 401:
                return 'Non autorisé. Veuillez vous authentifier.';
            case 403:
                return 'Accès refusé. Vous n\'avez pas les permissions nécessaires.';
            case 404:
                return 'Ressource non trouvée.';
            case 405:
                return 'Méthode non autorisée.';
            case 422:
                return 'Erreur de validation des données.';
            case 500:
                return 'Erreur interne du serveur.';
            default:
                return 'Une erreur est survenue.';
        }
    }
}
