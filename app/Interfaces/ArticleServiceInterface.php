<?php

namespace App\Interfaces;

use App\Models\Article;

interface ArticleServiceInterface
{
    public function createArticle(array $data): Article;
    public function getAllArticles(?string $disponible): array;
    public function getArticleById(int $id): Article;
    public function updateArticle(int $id, array $data): Article;
    public function deleteArticle(int $id): bool;
}
