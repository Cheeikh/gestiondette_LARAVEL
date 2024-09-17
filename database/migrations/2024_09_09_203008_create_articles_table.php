<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticlesTable extends Migration
{
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('libelle')->unique();
            $table->decimal('prix', 8, 2);  // Prix avec deux décimales
            $table->integer('qteStock');
            $table->integer('quantite_seuil')->default(0); // Ajout du champ quantite_seuil
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('articles');
    }
}
