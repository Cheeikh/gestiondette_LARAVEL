<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;

class ApiResponseMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if ($response instanceof JsonResponse) {
            $data = $response->getData();

            $formattedResponse = [
                'status' => $response->status(),
                'data' => $data->data ?? null,
                'message' => $data->message ?? '',
            ];

            return response()->json($formattedResponse, $response->status());
        }

        return $response;
    }
}
