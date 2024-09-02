<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Clients",
 *     description="Endpoints liés à la gestion des clients"
 * )
 */
class ClientController extends Controller
{
    /**
     * Méthode pour créer un client.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

      /**
     * @OA\Post(
     *     path="/api/clients",
     *     operationId="storeClient",
     *     tags={"Clients"},
     *     summary="Créer un nouveau client",
     *     description="Ajoute un nouveau client dans la base de données",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nom","email","telephone"},
     *             @OA\Property(property="nom", type="string", example="Jane Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="jane.doe@example.com"),
     *             @OA\Property(property="telephone", type="string", example="+123456789"),
     *             @OA\Property(property="adresse", type="string", example="123 Main St"),
     *             @OA\Property(property="surnom", type="string", example="JD")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Client créé avec succès",
     *         @OA\JsonContent(ref="#/components/schemas/Client")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error"
     *     )
     * )
     */
    public function store(Request $request)
    {
        // Validation des données entrantes
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:clients',
            'telephone' => 'required|string|max:15',
            'adresse' => 'nullable|string|max:255',
            'surnom' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Création du client
        $client = Client::create([
            'nom' => $request->nom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'adresse' => $request->adresse,
            'surnom' => $request->surnom,
        ]);

        return response()->json(['client' => $client], 201);
    }

    /**
     * Méthode pour lister tous les clients.
     *
     * @return \Illuminate\Http\JsonResponse
     */

     /**
     * @OA\Get(
     *     path="/api/clients",
     *     operationId="getClientsList",
     *     tags={"Clients"},
     *     summary="Lister tous les clients",
     *     description="Retourne une liste de tous les clients.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des clients",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Client")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $clients = Client::all(); // Récupérer tous les clients
        return response()->json(['clients' => $clients], 200);
    }

    /**
     * Méthode pour voir les détails d'un client spécifique.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */

     /**
     * @OA\Get(
     *     path="/api/clients/{id}",
     *     operationId="getClientById",
     *     tags={"Clients"},
     *     summary="Voir les détails d'un client",
     *     description="Retourne les détails d'un client spécifique.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID du client"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Détails du client",
     *         @OA\JsonContent(ref="#/components/schemas/Client")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Client not found"
     *     )
     * )
     */
    public function show($id)
    {
        $client = Client::find($id); // Trouver le client par ID

        if (!$client) {
            return response()->json(['message' => 'Client not found'], 404);
        }

        return response()->json(['client' => $client], 200);
    }
}

/**
 * @OA\Schema(
 *     schema="Client",
 *     type="object",
 *     required={"id", "nom", "email", "telephone"},
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="nom", type="string", example="Jane Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="jane.doe@example.com"),
 *     @OA\Property(property="telephone", type="string", example="+123456789"),
 *     @OA\Property(property="adresse", type="string", example="123 Main St"),
 *     @OA\Property(property="surnom", type="string", example="JD")
 * )
 */