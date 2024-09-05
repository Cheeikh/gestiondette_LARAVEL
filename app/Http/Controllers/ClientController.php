<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterClientRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Interfaces\ClientServiceInterface;
use App\Uploads\UploadInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use PDF;
use App\Http\Controllers\UserController;

class ClientController extends Controller
{
    protected $clientService;
    protected $uploadService;
    protected $userController;

    public function __construct(ClientServiceInterface $clientService, UploadInterface $uploadService, UserController $userController)
    {
        $this->clientService = $clientService;
        $this->uploadService = $uploadService;
        $this->userController = $userController;
    }

    // Enregistrer un nouveau client avec ou sans compte utilisateurpublic function store(RegisterClientRequest $request)
    public function store(RegisterClientRequest $request)
{
    

    // Enregistrer le client avec la photo
    $clientData = array_merge($request->validated());

    // Vérifier si les données du compte utilisateur sont présentes
    if ($request->has('user')) {
        // Extraire et valider les données utilisateur
        $userData = $request->input('user');

        // Créer une nouvelle instance de RegisterUserRequest avec les données utilisateur
        $userRequest = new RegisterUserRequest();
        $userRequest->merge($userData);

        // Valider les données utilisateur
        $userRequest->validate();

        // Créer le compte utilisateur en utilisant le UserController
        $userResponse = $this->userController->register($userRequest);

        if ($userResponse->getStatusCode() !== 201) {
            return response()->json([
                'status' => 400,
                'message' => 'Failed to create user account'
            ], 400);
        }

        $userData = $userResponse->getData()->data;
        $clientData['user_id'] = $userData->id;
    }

    $client = $this->clientService->registerClient($clientData);

    // Génération de la carte de fidélité en PDF
    $pdf = PDF::loadView('fidelite.carte', compact('client'));

    // Envoyer un email avec la carte de fidélité en pièce jointe
    Mail::send('emails.client_fidelite', compact('client'), function ($message) use ($client, $pdf) {
        $message->to($client->email)
                ->subject('Votre carte de fidélité')
                ->attachData($pdf->output(), 'carte_fidelite.pdf');
    });

    // Préparer la réponse
    $response = [
        'surname' => $client->surname,
        'adresse' => $client->adresse,
        'telephone' => $client->telephone,
        'email' => $client->email,
    ];

    if (isset($clientData['user_id'])) {
        $response['user'] = $userData;
    }

    return response()->json([
        'status' => 201,
        'data' => $response,
        'message' => 'Client enregistré avec succès et carte de fidélité envoyée'
    ], 201);
}


    // Lister tous les clients avec filtres (comptes et active)
    public function index(Request $request)
    {
        $comptes = $request->query('comptes');
        $active = $request->query('active');
        
        $clients = $this->clientService->getAllClients($comptes, $active);

        return response()->json([
            'status' => 200,
            'data' => $clients,
            'message' => 'Liste des clients'
        ], 200);
    }

    // Obtenir les informations d'un client par son ID
    public function show($id)
    {
        $client = $this->clientService->getClientById($id);

        return response()->json([
            'status' => 200,
            'data' => $client,
            'message' => 'Détails du client'
        ], 200);
    }

    // Obtenir les informations du client ainsi que son compte utilisateur
    public function showClientWithUser($id)
    {
        $clientWithUser = $this->clientService->getClientWithUser($id);

        return response()->json([
            'status' => 200,
            'data' => $clientWithUser,
            'message' => 'Détails du client avec compte utilisateur'
        ], 200);
    }
}