<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDetteRequest;
use App\Http\Resources\DetteResource;
use App\Models\Article;
use App\Models\Client;
use App\Models\Dette;
use App\Models\Paiement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


/**
 * @OA\Post(
 *     path="/api/v1/dettes",
 *     summary="Enregistrer une nouvelle dette",
 *     tags={"Dettes"},
 *     security={{"passport":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"montant","clientId","articles"},
 *             @OA\Property(property="montant", type="number", format="float", example=1000000),
 *             @OA\Property(property="clientId", type="integer", example=1),
 *             @OA\Property(
 *                 property="articles",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="articleId", type="integer", example=3),
 *                     @OA\Property(property="qteVente", type="integer", example=500),
 *                     @OA\Property(property="prixVente", type="number", format="float", example=100)
 *                 )
 *             ),
 *             @OA\Property(
 *                 property="paiement",
 *                 type="object",
 *                 @OA\Property(property="montant", type="number", format="float", example=500000)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Dette enregistrée avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=201),
 *             @OA\Property(property="data", ref="#/components/schemas/DetteResource"),
 *             @OA\Property(property="message", type="string", example="Dette enregistrée avec succès")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation Erreur",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=422),
 *             @OA\Property(property="data", type="null", example=null),
 *             @OA\Property(property="message", type="string", example="Le montant du paiement dépasse le montant de la dette.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erreur Serveur",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=500),
 *             @OA\Property(property="data", type="null", example=null),
 *             @OA\Property(property="message", type="string", example="Une erreur est survenue lors de l'enregistrement de la dette.")
 *         )
 *     )
 * )
 */
class DetteController extends Controller
{
    public function store(StoreDetteRequest $request): JsonResponse
    {
        $data = $request->validated();

        DB::beginTransaction();

        try {
            // Vérifier que le client existe
            $client = Client::findOrFail($data['clientId']);

            // Créer la dette
            $dette = Dette::create([
                'client_id' => $data['clientId'],
                'montant' => $data['montant'],
            ]);

            // Gérer les articles
            foreach ($data['articles'] as $articleData) {
                $article = Article::findOrFail($articleData['articleId']);

                // Mettre à jour la quantité en stock
                $article->decrement('qte_stock', $articleData['qteVente']);

                // Attacher l'article à la dette avec les détails
                $dette->articles()->attach($article->id, [
                    'qte_vente' => $articleData['qteVente'],
                    'prix_vente' => $articleData['prixVente'],
                ]);
            }

            // Gérer le paiement s'il existe
            if (isset($data['paiement'])) {
                $paiementData = $data['paiement'];
                Paiement::create([
                    'dette_id' => $dette->id,
                    'montant' => $paiementData['montant'],
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 201,
                'data' => new DetteResource($dette->load(['articles', 'client'])),
                'message' => "Dette enregistrée avec succès",
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 500,
                'data' => null,
                'message' => "Une erreur est survenue lors de l'enregistrement de la dette.",
            ], 500);
        }
    }
}
