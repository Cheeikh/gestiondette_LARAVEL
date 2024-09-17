<?php

use App\Http\Controllers\DemandeController;
use App\Http\Controllers\DetteController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ArticleController;
use App\Http\Middleware\FormatJsonResponse;

use App\Models\ArchivedDette;
Route::get('/test-mongo', function () {
    $data = ArchivedDette::all();
    return response()->json($data);
});

Route::prefix('v1')->middleware(FormatJsonResponse::class)->group(function () {

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

        Route::prefix('clients')->group(function () {
            Route::get('/', [ClientController::class, 'index']);
            Route::post('/', [ClientController::class, 'store']);
            Route::get('/notifications/unread', [ClientController::class, 'getUnreadNotifications']);
            Route::get('/notifications/read', [ClientController::class, 'getReadNotifications']);
            Route::get('/{id}', [ClientController::class, 'show']);
            Route::get('/{id}/user', [ClientController::class, 'showClientWithUser']);
            Route::get('/{clientId}/dettes', [ClientController::class, 'listDettes']);
        });

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
            //Route::get('/{id}', [DetteController::class, 'show']);
            Route::get('/{id}/articles', [DetteController::class, 'listArticles']);
            Route::get('/{id}/paiements', [DetteController::class, 'listPaiements']);
            Route::post('/{id}/paiements', [DetteController::class, 'addPaiement']);
            Route::get('/archive', [DetteController::class, 'showArchived']);
            Route::get('/archive/clients/{id}/dettes', [DetteController::class, 'showClientArchivedDettes']);
            Route::get('/restaure/{date}', [DetteController::class, 'restoreDebtsByDate']);
            Route::get('/restaure/dette/{id}', [DetteController::class, 'restoreDebtById']);
            Route::get('/restaure/client/{client_id}', [DetteController::class, 'restoreClientDebts']);
        });

        Route::prefix('notification')->group(function () {
            Route::post('/client/all', [NotificationController::class, 'sendDebtReminderToAllClients']);
            Route::get('/client/{clientId}', [NotificationController::class, 'sendDebtReminder']);
            Route::post('/client/message', [NotificationController::class, 'sendMessageToClients']);
        });

        Route::prefix('demandes')->group(function () {
            Route::post('/', [DemandeController::class, 'store']);
            Route::get('/', [DemandeController::class, 'getClientDemandes']);
            Route::get('/notifications', [DemandeController::class, 'getNotifications']);
            Route::get('/all', [DemandeController::class, 'getAllDemandes']);
            Route::get('/notifications/client', [DemandeController::class, 'getClientNotifications']);
            Route::post('/{id}/relance', [DemandeController::class, 'sendRelance']);
            Route::get('/{id}/disponible', [DemandeController::class, 'checkDisponibilite']);
            Route::patch('/{id}', [DemandeController::class, 'update']);
        });


    });
});
