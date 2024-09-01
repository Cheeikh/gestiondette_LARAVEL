<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDettesTable extends Migration
{
    public function up()
    {
        Schema::create('dettes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->decimal('montant', 15, 2);
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
        });

        // Table pivot pour Dette et Article
        Schema::create('article_dette', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dette_id');
            $table->unsignedBigInteger('article_id');
            $table->integer('qte_vente');
            $table->decimal('prix_vente', 10, 2);
            $table->timestamps();

            $table->foreign('dette_id')->references('id')->on('dettes')->onDelete('cascade');
            $table->foreign('article_id')->references('id')->on('articles')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('article_dette');
        Schema::dropIfExists('dettes');
    }
}
