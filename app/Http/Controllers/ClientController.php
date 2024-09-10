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
        $client = ClientFacade::registerClient(
            $request->validated(),
            $request->file('user.photo')
        );

        return response()->json($client, 201);
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
        $client = ClientFacade::getClientById($id);
        return response()->json($client, 200);
    }

    public function showClientWithUser($id)
    {
        $clientWithUser = ClientFacade::getClientWithUser($id);
        return response()->json($clientWithUser, 200);
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
        $dettes = DetteFacade::getDettesByClientId($clientId);
        return response()->json($dettes, 201);
    }
}
