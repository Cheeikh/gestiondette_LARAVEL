<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiResponseMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Vérifiez si c'est une réponse JSON
        if ($response instanceof \Illuminate\Http\JsonResponse) {
            $originalData = $response->getData();

            $formattedResponse = [
                'status' => $response->status(),
                'data' => $originalData->data ?? $originalData,
                'message' => $originalData->message ?? '',
            ];

            return response()->json($formattedResponse, $response->status());
        }

        return $response;
    }
}