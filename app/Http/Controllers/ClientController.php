<?php

namespace App\Http\Controllers;

use App\Facades\ClientFacade;
use App\Http\Requests\RegisterClientRequest;
use Illuminate\Http\Request;
use App\Facades\DetteFacade;

class ClientController extends Controller
{
    public function store(RegisterClientRequest $request)
    {
        try {
            $client = ClientFacade::registerClient(
                $request->validated(),
                $request->file('user.photo')
            );
            return response()->json($client, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to register client', 'message' => $e->getMessage()], 400);
        }
    }


    public function index(Request $request)
    {
        $hasUser = $request->query('comptes') === 'oui' ? true : ($request->query('comptes') === 'non' ? false : null);
        $active = $request->query('active') === 'oui' ? true : ($request->query('active') === 'non' ? false : null);

        $clients = ClientFacade::getAllClients($hasUser, $active);

        return response()->json($clients, 200);
    }

    public function show($id)
    {
        try {
            $client = ClientFacade::getClientById($id);
            if (!$client) {
                return response()->json(['error' => 'Client not found'], 404);
            }
            return response()->json($client, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve client', 'message' => $e->getMessage()], 400);
        }
    }


    public function showClientWithUser($id)
    {
        try {
            $clientWithUser = ClientFacade::getClientWithUser($id);
            if (!$clientWithUser) {
                return response()->json(['error' => 'Client or user not found'], 404);
            }
            return response()->json($clientWithUser, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve client with user', 'message' => $e->getMessage()], 400);
        }
    }


    public function createClientAccount(Request $request)
    {
        $clientId = $request->input('client_id');
        $userData = $request->all();
        $photo = $request->file('photo');

        try {
            $client = ClientFacade::createOrUpdateUserForClient($clientId, $userData, $photo);
            return response()->json([
                'message' => 'User account created or updated successfully for the client.',
                'client' => $client
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Client account creation failed',
                'message' => $e->getMessage()
            ], 400);
        }
    }


    public function listDettes($clientId)
    {
        try {
            $dettes = DetteFacade::getDettesByClientId($clientId);
            return response()->json($dettes, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve debts', 'message' => $e->getMessage()], 400);
        }
    }

    public function getUnreadNotifications(Request $request)
    {
        $client = $request->user()->client;  // Assurez-vous que l'utilisateur connecté a un 'client' associé
        $notifications = $client->unreadNotifications;

        return response()->json($notifications);
    }

    public function getReadNotifications(Request $request)
    {
        $client = $request->user()->client;  // Assurez-vous que l'utilisateur connecté a un 'client' associé
        $notifications = $client->readNotifications;

        return response()->json($notifications);
    }

}
