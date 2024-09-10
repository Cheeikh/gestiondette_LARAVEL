<?php

namespace App\Repositories;

use App\Interfaces\ArticleRepositoryInterface;
use App\Models\Article;

class ArticleRepository implements ArticleRepositoryInterface
{
    public function create(array $data): Article
    {
        return Article::create($data);
    }

    public function findAll(): array
    {
        return Article::all()->toArray();
    }

    public function findById(int $id): Article
    {
        return Article::findOrFail($id);
    }

    public function update(Article $article, array $data): Article
    {
        $article->update($data);
        return $article;
    }

    public function delete(Article $article): bool
    {
        return $article->delete();
    }

    public function findByLibelle(string $libelle): ?Article
    {
        return Article::where('libelle', $libelle)->first();
    }

    public function findAvailableArticles(): array
    {
        return Article::where('qteStock', '>', 0)->get()->toArray();
    }

    public function findUnavailableArticles(): array
    {
        return Article::where('qteStock', '=', 0)->get()->toArray();
    }

    public function updateStock(int $articleId, int $qteVente): bool
    {
        $article = Article::findOrFail($articleId);

        if ($article->qteStock < $qteVente) {
            throw new \Exception('Quantité en stock insuffisante');
        }

        $article->qteStock -= $qteVente;
        return $article->save();
    }

}
