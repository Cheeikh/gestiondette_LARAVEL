<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Articles",
 *     description="API Endpoints pour la gestion des articles"
 * )
 */



/**
 * @OA\Schema(
 *     schema="ArticleRequest",
 *     required={"libelle", "prix", "qteStock"},
 *     @OA\Property(
 *         property="libelle", 
 *         type="string",
 *         maxLength=255,
 *         description="Nom de l'article",
 *         example="Lait Laicran 700g"
 *     ),
 *     @OA\Property(
 *         property="prix", 
 *         type="number",
 *         format="float",
 *         description="Prix de l'article",
 *         example=1700
 *     ),
 *     @OA\Property(
 *         property="qteStock", 
 *         type="integer",
 *         description="Quantité en stock",
 *         example=1000
 *     )
 * )
 */
class ArticleController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/articles",
     *     operationId="getArticlesList",
     *     tags={"Articles"},
     *     summary="Obtenir la liste des articles",
     *     description="Retourne la liste de tous les articles",
     *     @OA\Response(
     *         response=200,
     *         description="Opération réussie",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Article")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $articles = Article::all();
        return response()->json($articles, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/articles",
     *     operationId="storeArticle",
     *     tags={"Articles"},
     *     summary="Créer un nouvel article",
     *     description="Crée un nouvel article et retourne les données créées",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ArticleRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Article créé avec succès",
     *         @OA\JsonContent(ref="#/components/schemas/Article")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Données invalides"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'libelle' => 'required|string|max:255|unique:articles,libelle',
            'prix' => 'required|numeric|min:0',
            'qteStock' => 'required|integer|min:0',
        ], [
            'libelle.required' => 'Le libellé est obligatoire.',
            'libelle.unique' => 'Ce libellé est déjà utilisé.',
            'libelle.max' => 'Le libellé ne peut pas dépasser 255 caractères.',
            'prix.required' => 'Le prix est obligatoire.',
            'prix.numeric' => 'Le prix doit être un nombre.',
            'prix.min' => 'Le prix ne peut pas être négatif.',
            'qteStock.required' => 'La quantité en stock est obligatoire.',
            'qteStock.integer' => 'La quantité en stock doit être un entier.',
            'qteStock.min' => 'La quantité en stock ne peut pas être négative.',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $article = Article::create($request->all());
        return response()->json($article, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/articles/{id}",
     *     operationId="getArticleById",
     *     tags={"Articles"},
     *     summary="Obtenir un article spécifique",
     *     description="Retourne un article spécifique",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Opération réussie",
     *         @OA\JsonContent(ref="#/components/schemas/Article")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Article non trouvé"
     *     )
     * )
     */
    public function show($id)
    {
        $article = Article::findOrFail($id);
        return response()->json($article, 200);
    }

    /**
     * @OA\Put(
     *     path="/api/articles/{id}",
     *     operationId="updateArticle",
     *     tags={"Articles"},
     *     summary="Mettre à jour un article existant",
     *     description="Met à jour un article existant et retourne les données mises à jour",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ArticleRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Article mis à jour avec succès",
     *         @OA\JsonContent(ref="#/components/schemas/Article")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Article non trouvé"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Données invalides"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $article = Article::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'libelle' => 'required|string|max:255|unique:articles,libelle,' . $id,
            'prix' => 'required|numeric|min:0',
            'qteStock' => 'required|integer|min:0',
        ], [
            'libelle.required' => 'Le libellé est obligatoire.',
            'libelle.unique' => 'Ce libellé est déjà utilisé.',
            'libelle.max' => 'Le libellé ne peut pas dépasser 255 caractères.',
            'prix.required' => 'Le prix est obligatoire.',
            'prix.numeric' => 'Le prix doit être un nombre.',
            'prix.min' => 'Le prix ne peut pas être négatif.',
            'qteStock.required' => 'La quantité en stock est obligatoire.',
            'qteStock.integer' => 'La quantité en stock doit être un entier.',
            'qteStock.min' => 'La quantité en stock ne peut pas être négative.',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $article->update($request->all());
        return response()->json($article, 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/articles/{id}",
     *     operationId="deleteArticle",
     *     tags={"Articles"},
     *     summary="Supprimer un article",
     *     description="Supprime un article existant",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Article supprimé avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Article non trouvé"
     *     )
     * )
     */
    public function destroy($id)
    {
        $article = Article::findOrFail($id);
        $article->delete();
        return response()->json(null, 204);
    }
}