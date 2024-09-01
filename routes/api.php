<?php

use Laravel\Passport\Http\Controllers\AccessTokenController;
use Laravel\Passport\Http\Controllers\AuthorizationController;
use App\Http\Controllers\DetteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Routes publiques pour l'authentification
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Routes protégées par auth:api
Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Préfixe v1 pour les routes de l'API
    Route::prefix('v1')->group(function () {
        Route::post('/dettes', [DetteController::class, 'store']);
        // Ajoutez ici d'autres routes protégées pour votre API
    });
});

// Routes Passport OAuth
Route::prefix('oauth')->group(function () {
    Route::post('/token', [AccessTokenController::class, 'issueToken']);
    Route::get('/authorize', [AuthorizationController::class, 'authorize'])->middleware('auth:api');
});