<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaiementsTable extends Migration
{
    public function up()
    {
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dette_id');  // Lien avec la table dettes
            $table->decimal('montant', 10, 2);  // Montant du paiement
            $table->date('date');
            $table->timestamps();

            $table->foreign('dette_id')->references('id')->on('dettes')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('paiements');
    }
}
