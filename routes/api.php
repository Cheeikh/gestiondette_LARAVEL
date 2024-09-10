<?php

use App\Http\Controllers\DetteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ArticleController;

Route::prefix('v1')->group(function () {

    // Routes pour les utilisateurs
    Route::prefix('users')->group(function () {
        Route::post('/', [UserController::class, 'register']);
    });

    Route::post('/login', [UserController::class, 'login']);

    Route::post('/register', [ClientController::class, 'createClientAccount']);


    Route::middleware('auth:api')->group(function () {

        Route::prefix('users')->group(function () {
            Route::get('/', [UserController::class, 'index']);
            Route::get('/me', [UserController::class, 'user']);
            Route::post('/logout', [UserController::class, 'logout']);
            Route::post('/refresh', [UserController::class, 'refresh']);
        });

        // Routes pour les clients
        Route::prefix('clients')->group(function () {
            Route::get('/', [ClientController::class, 'index']);
            Route::post('/', [ClientController::class, 'store']);
            Route::get('/{id}', [ClientController::class, 'show']);
            Route::get('/{id}/user', [ClientController::class, 'showClientWithUser']);
            Route::get('/{clientId}/dettes', [ClientController::class, 'listDettes']);
        });

        // Routes pour les articles
        Route::prefix('articles')->group(function () {
            Route::get('/', [ArticleController::class, 'index']);
            Route::post('/', [ArticleController::class, 'store']);
            Route::get('/{id}', [ArticleController::class, 'show']);
            Route::put('/{id}', [ArticleController::class, 'update']);
            Route::delete('/{id}', [ArticleController::class, 'destroy']);
            Route::post('/libelle', [ArticleController::class, 'getByLibelle']);
            Route::patch('/{id}/qteStock', [ArticleController::class, 'updateStockById']);
            Route::post('/all/qteStock', [ArticleController::class, 'updateStockForAll']);
        });

        // Routes pour les dettes
        Route::prefix('dettes')->group(function () {
            Route::get('/', [DetteController::class, 'listAll']);
            Route::post('/', [DetteController::class, 'store']);
            Route::get('/{id}', [DetteController::class, 'show']);
            Route::post('/{id}/articles', [DetteController::class, 'listArticles']);
            Route::get('/{id}/paiements', [DetteController::class, 'listPaiements']);
            Route::post('/{id}/paiements', [DetteController::class, 'addPaiement']);
        });
    });
});
