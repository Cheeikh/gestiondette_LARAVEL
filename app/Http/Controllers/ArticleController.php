<?php

namespace App\Http\Controllers;

use App\Facades\ArticleFacade;
use App\Http\Requests\ArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Requests\UpdateStockRequest;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $disponible = $request->query('disponible');
        $articles = ArticleFacade::getAllArticles($disponible);
        return response()->json($articles, Response::HTTP_OK);
    }

    public function store(ArticleRequest $request)
    {
        try {
            $article = ArticleFacade::createArticle($request->validated());
            return response()->json($article, Response::HTTP_CREATED);
        } catch (QueryException $e) {
            Log::error('Erreur lors de la création de l\'article : ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la création de l\'article.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $article = ArticleFacade::getArticleById($id);
            return response()->json($article, Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Article non trouvé'], Response::HTTP_NOT_FOUND);
        }
    }

    public function update(UpdateArticleRequest $request, $id)
    {
        try {
            $article = ArticleFacade::updateArticle($id, $request->validated());
            return response()->json($article, Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Article non trouvé'], Response::HTTP_NOT_FOUND);
        } catch (QueryException $e) {
            Log::error('Erreur lors de la mise à jour de l\'article ID ' . $id . ' : ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la mise à jour de l\'article.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try {
            ArticleFacade::deleteArticle($id);
            return response()->json(['message' => 'Article supprimé avec succès.'], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Article non trouvé'], Response::HTTP_NOT_FOUND);
        } catch (QueryException $e) {
            Log::error('Erreur lors de la suppression de l\'article ID ' . $id . ' : ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la suppression de l\'article.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getByLibelle(Request $request)
    {
        $request->validate([
            'libelle' => 'required|string',
        ]);

        $libelle = $request->input('libelle');

        try {
            $article = ArticleFacade::getArticleByLibelle($libelle);
            if (!$article) {
                return response()->json(['error' => 'Article non trouvé avec ce libellé'], Response::HTTP_NOT_FOUND);
            }
            return response()->json($article, Response::HTTP_OK);
        } catch (QueryException $e) {
            Log::error('Erreur lors de la récupération de l\'article avec le libellé ' . $libelle . ' : ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la récupération de l\'article.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateStockById(UpdateStockRequest $request, $id)
    {
        try {
            $article = ArticleFacade::updateStock($id, $request->validated()['qteStock']);
            return response()->json($article, Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Article non trouvé'], Response::HTTP_NOT_FOUND);
        } catch (QueryException $e) {
            Log::error('Erreur lors de la mise à jour du stock de l\'article ID ' . $id . ' : ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la mise à jour du stock.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateStockForAll(Request $request)
    {
        $articlesData = $request->input('articles');  // Attendez-vous à un tableau d'articles avec 'id' et 'qteStock'

        if (!is_array($articlesData)) {
            return response()->json(['error' => 'Les données des articles sont invalides.'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $updatedArticles = ArticleFacade::updateStockForAll($articlesData);
            return response()->json($updatedArticles, Response::HTTP_OK);
        } catch (QueryException $e) {
            Log::error('Erreur lors de la mise à jour du stock pour tous les articles : ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la mise à jour du stock pour tous les articles.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
