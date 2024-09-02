<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $guard = null)
    {
        Log::info('Tentative d\'authentification', [
            'headers' => $request->headers->all(),
            'token' => $request->bearerToken(),
            'guard' => $guard
        ]);

        if (!$request->bearerToken()) {
            Log::warning('Pas de token Bearer trouvé');
            return response()->json(['message' => 'Token manquant'], 401);
        }

        if (Auth::guard($guard)->guest()) {
            Log::warning('Authentification échouée', [
                'token' => $request->bearerToken()
            ]);
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        Log::info('Authentification réussie', [
            'user_id' => Auth::id()
        ]);

        return $next($request);
    }
}