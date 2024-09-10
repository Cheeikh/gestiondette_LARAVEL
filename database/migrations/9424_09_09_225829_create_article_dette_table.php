<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticleDetteTable extends Migration
{
    public function up()
    {
        Schema::create('article_dette', function (Blueprint $table) {
            $table->unsignedBigInteger('dette_id');
            $table->unsignedBigInteger('article_id');
            $table->integer('qte_vente');
            $table->decimal('prix_vente', 8, 2);  // Prix de vente à l'unité pour cet article

            $table->foreign('dette_id')->references('id')->on('dettes')->onDelete('cascade');
            $table->foreign('article_id')->references('id')->on('articles')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('article_dette');
    }
}
