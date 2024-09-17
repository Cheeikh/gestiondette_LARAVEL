<?php

namespace App\Services;

use App\Interfaces\ArticleRepositoryInterface;
use App\Interfaces\ArticleServiceInterface;
use App\Models\Article;

class ArticleService implements ArticleServiceInterface
{
    protected $articleRepository;

    public function __construct(ArticleRepositoryInterface $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    public function createArticle(array $data): Article
    {
        return $this->articleRepository->create($data);
    }

    public function getAllArticles(?string $disponible)
    {
        if ($disponible === 'oui') {
            return $this->articleRepository->findAvailableArticles();
        } elseif ($disponible === 'non') {
            return $this->articleRepository->findUnavailableArticles();
        } else {
            return $this->articleRepository->findAll();
        }
    }

    public function getArticleById(int $id): Article
    {
        return $this->articleRepository->findById($id);
    }

    public function updateArticle(int $id, array $data): Article
    {
        $article = $this->getArticleById($id);
        return $this->articleRepository->update($article, $data);
    }

    public function deleteArticle(int $id): bool
    {
        $article = $this->getArticleById($id);
        return $this->articleRepository->delete($article);
    }

    public function getArticleByLibelle(string $libelle): ?Article
    {
        return $this->articleRepository->findByLibelle($libelle);
    }

    public function updateStock(int $id, int $qteStock): Article
    {
        $article = $this->getArticleById($id);
        return $this->articleRepository->update($article, ['qteStock' => $qteStock]);
    }

    public function updateStockForAll(array $articlesData): array
    {
        $updatedArticles = [];
        foreach ($articlesData as $articleData) {
            $article = $this->getArticleById($articleData['id']);
            $updatedArticle = $this->articleRepository->update($article, ['qteStock' => $articleData['qteStock']]);
            $updatedArticles[] = $updatedArticle;
        }
        return $updatedArticles;
    }
}
