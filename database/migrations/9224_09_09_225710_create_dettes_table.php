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
            $table->date('date');
            $table->date('date_echeance');  // Date d'échéance de la dette
            $table->decimal('montant', 10, 2);
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('dettes');
    }
}
