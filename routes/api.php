<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DetteController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ArticleController;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Laravel\Passport\Http\Controllers\AuthorizationController;

// Routes publiques pour l'authentification
Route::post('/register', [AuthController::class, 'register']);  // Crée un nouvel utilisateur (admin ou vendeur)
Route::post('/login', [AuthController::class, 'login']);        // Authentification de l'utilisateur

// Routes protégées par auth:api
Route::middleware('auth:api')->group(function () {
    Route::get('/users', [AuthController::class, 'index']);        // Liste tous les utilisateurs

    // Déconnexion de l'utilisateur
    Route::post('/logout', [AuthController::class, 'logout']);

    // Routes spécifiques pour les clients
    Route::apiResource('clients', ClientController::class);

    // Route pour créer un compte utilisateur pour un client existant
    Route::post('/clients/create-account', [AuthController::class, 'createClientAccount']);

    // Routes pour les articles
    Route::apiResource('articles', ArticleController::class);

    // Préfixe v1 pour les routes de l'API
    Route::prefix('v1')->group(function () {
        // Gestion des dettes
        Route::post('/dettes', [DetteController::class, 'store']);
        
        // Ajoutez ici d'autres routes protégées pour votre API
    });
});

// Routes Passport OAuth
Route::prefix('oauth')->group(function () {
    Route::post('/token', [AccessTokenController::class, 'issueToken']);
    Route::get('/authorize', [AuthorizationController::class, 'authorize'])->middleware('auth:api');
});