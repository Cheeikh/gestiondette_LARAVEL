<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log; // Import the Log facade

class FormatJsonResponse
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only process JsonResponse instances
        if ($response instanceof JsonResponse) {
            $data = json_decode($response->getContent(), true); // Decode to array

            Log::info('Original Response Data:', $data); // Log original data for debugging

            $statusCode = $response->getStatusCode();
            $defaultMessage = $this->getDefaultMessageForStatusCode($statusCode);

            // Check if 'data' key exists, and handle the case where it doesn't
            $responseData = $data['data'] ?? $data; // Use entire response if 'data' key is absent

            // Formulate the new response structure
            $formattedResponse = [
                'status' => $statusCode,
                'data' => $responseData, // Use corrected data handling
                'message' => $data['message'] ?? $defaultMessage
            ];

            // Set the new JSON content and preserve headers
            return response()->json($formattedResponse, $statusCode)
                ->withHeaders($response->headers->all());
        }

        return $response;
    }

    private function getDefaultMessageForStatusCode(int $statusCode): string
    {
        $messages = [
            200 => 'Requête traitée avec succès.',
            201 => 'Ressource créée avec succès.',
            204 => 'Aucun contenu.',
            400 => 'Requête invalide.',
            401 => 'Non autorisé. Veuillez vous authentifier.',
            403 => 'Accès refusé. Vous n\'avez pas les permissions nécessaires.',
            404 => 'Ressource non trouvée.',
            405 => 'Méthode non autorisée.',
            422 => 'Erreur de validation des données.',
            500 => 'Erreur interne du serveur.',
            503 => 'Service indisponible.'
        ];
        return $messages[$statusCode] ?? 'Une erreur est survenue.';
    }
}
