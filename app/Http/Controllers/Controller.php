<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="API Dette Management",
 *     description="Documentation de l'API pour la gestion des dettes.",
 *     @OA\Contact(
 *         email="support@votreprojet.com"
 *     ),
 *     @OA\License(
 *         name="Apache 2.0",
 *         url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *     )
 * )
 *
 * @OA\Server(
 *     url="http://localhost:8000",
 *     description="Serveur local"
 * )
 *
 * @OA\Tag(
 *     name="Dettes",
 *     description="Gestion des dettes"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="passport",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 *
 * @OA\Schema(
 *     schema="DetteResource",
 *     type="object",
 *     title="DetteResource",
 *     description="Ressource Dette",
 *     @OA\Property(property="id", type="integer", description="ID de la dette"),
 *     @OA\Property(property="montant", type="number", format="float", description="Montant de la dette"),
 *     @OA\Property(property="client", ref="#/components/schemas/ClientResource"),
 *     @OA\Property(property="articles", type="array", @OA\Items(ref="#/components/schemas/ArticleResource")),
 *     @OA\Property(property="paiements", type="array", @OA\Items(ref="#/components/schemas/PaiementResource")),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Date de création"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Date de mise à jour")
 * )
 *
 * @OA\Schema(
 *     schema="ClientResource",
 *     type="object",
 *     title="ClientResource",
 *     description="Ressource Client",
 *     @OA\Property(property="id", type="integer", description="ID du client"),
 *     @OA\Property(property="nom", type="string", description="Nom du client"),
 *     @OA\Property(property="email", type="string", format="email", description="Email du client"),
 *     @OA\Property(property="telephone", type="string", description="Numéro de téléphone du client")
 * )
 *
 * @OA\Schema(
 *     schema="ArticleResource",
 *     type="object",
 *     title="ArticleResource",
 *     description="Ressource Article",
 *     @OA\Property(property="id", type="integer", description="ID de l'article"),
 *     @OA\Property(property="nom", type="string", description="Nom de l'article"),
 *     @OA\Property(property="qte_stock", type="integer", description="Quantité en stock"),
 *     @OA\Property(property="prix_vente", type="number", format="float", description="Prix de vente"),
 *     @OA\Property(property="pivot", type="object", description="Données pivot",
 *                  @OA\Property(property="qte_vente", type="integer", description="Quantité vendue"),
 *                  @OA\Property(property="prix_vente", type="number", format="float", description="Prix de vente"))
 * )
 *
 * @OA\Schema(
 *     schema="PaiementResource",
 *     type="object",
 *     title="PaiementResource",
 *     description="Ressource Paiement",
 *     @OA\Property(property="id", type="integer", description="ID du paiement"),
 *     @OA\Property(property="montant", type="number", format="float", description="Montant du paiement"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Date de création")
 * )
 * 
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     required={"id", "name", "prenom", "login", "email"},
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="prenom", type="string", example="John"),
 *     @OA\Property(property="login", type="string", example="johndoe"),
 *     @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
 *     @OA\Property(property="etat", type="string", example="actif")
 * )
 * 
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
 * 
 * @OA\Schema(
 *     schema="Article",
 *     required={"id", "libelle", "prix", "qteStock"},
 *     @OA\Property(property="id", type="integer", format="int64", readOnly=true),
 *     @OA\Property(property="libelle", type="string", maxLength=255),
 *     @OA\Property(property="prix", type="number", format="float"),
 *     @OA\Property(property="qteStock", type="integer"),
 *     @OA\Property(property="created_at", type="string", format="date-time", readOnly=true),
 *     @OA\Property(property="updated_at", type="string", format="date-time", readOnly=true)
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
