<?php

namespace App\Http\Controllers;

use App\Facades\ClientFacade;
use App\Http\Requests\RegisterClientRequest;
use Illuminate\Http\Request;

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
        $clients = ClientFacade::getAllClients(
            $request->query('comptes'),
            $request->query('active')
        );

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
}
