<?php

namespace App\Http\Controllers;

use App\Facades\ArticleFacade;
use App\Http\Requests\ArticleRequest;
use Illuminate\Http\Response;
use Illuminate\Http\Request;


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
        $article = ArticleFacade::createArticle($request->validated());
        return response()->json($article, Response::HTTP_CREATED);
    }

    public function show($id)
    {
        $article = ArticleFacade::getArticleById($id);
        return response()->json($article, Response::HTTP_OK);
    }

    public function update(ArticleRequest $request, $id)
    {
        $article = ArticleFacade::updateArticle($id, $request->validated());
        return response()->json($article, Response::HTTP_OK);
    }

    public function destroy($id)
    {
        ArticleFacade::deleteArticle($id);
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function getByLibelle(Request $request)
    {
        $libelle = $request->input('libelle');
        $article = ArticleFacade::getArticleByLibelle($libelle);
        return response()->json($article, Response::HTTP_OK);
    }

    public function updateStockById(Request $request, $id)
    {
        $qteStock = $request->input('qteStock');
        $article = ArticleFacade::updateStock($id, $qteStock);
        return response()->json($article, Response::HTTP_OK);
    }

    public function updateStockForAll(Request $request)
    {
        $articlesData = $request->all();  // Assuming an array of articles with 'id' and 'qteStock'
        $updatedArticles = ArticleFacade::updateStockForAll($articlesData);
        return response()->json($updatedArticles, Response::HTTP_OK);
    }


}
