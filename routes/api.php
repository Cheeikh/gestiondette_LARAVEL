<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientController;

// Préfixe pour la version 1 de l'API
Route::prefix('v1')->group(function () {

    // Routes publiques pour l'enregistrement et la connexion des utilisateurs
    Route::prefix('users')->group(function () {
        Route::post('/register', [UserController::class, 'register']);  // Enregistrement des utilisateurs
    });

    Route::post('/login', [UserController::class, 'login']);  // Connexion des utilisateurs

    // Routes protégées par l'authentification
    Route::middleware('auth:api')->group(function () {

        // Préfixe pour les routes des utilisateurs
        Route::prefix('users')->group(function () {
            Route::get('/', [UserController::class, 'index']);  // Lister tous les utilisateurs
            Route::get('/role', [UserController::class, 'getByRole']);  // Obtenir les utilisateurs par rôle
            Route::get('/me', [UserController::class, 'user']);  // Récupérer les informations de l'utilisateur connecté
            Route::post('/logout', [UserController::class, 'logout']);  // Déconnexion de l'utilisateur
            Route::post('/refresh', [UserController::class, 'refresh']);  // Rafraîchir le token d'accès
        });

        // Préfixe pour les routes des clients
        Route::prefix('clients')->group(function () {
            Route::get('/', [ClientController::class, 'index']);  // Lister tous les clients
            Route::post('/', [ClientController::class, 'store']);  // Enregistrer un nouveau client
            Route::get('/{id}', [ClientController::class, 'show']);  // Obtenir les informations d'un client par ID
            Route::get('/{id}/user', [ClientController::class, 'showClientWithUser']);  // Obtenir les informations d'un client et son compte utilisateur
        });
    });
});
