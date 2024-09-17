<?php

namespace App\Interfaces;

use App\Models\Article;

interface ArticleRepositoryInterface
{
    public function create(array $data): Article;
    public function findAll();
    public function findById(int $id): Article;
    public function update(Article $article, array $data): Article;
    public function delete(Article $article): bool;

}
