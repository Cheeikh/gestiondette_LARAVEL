<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('surname')->unique();
            $table->string('telephone')->unique();
            $table->string('email')->unique();  // Ajout du champ email
            $table->string('adresse')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->foreignId('category_id')->default(3)->constrained('categories');
            $table->decimal('max_montant', 10, 2)->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('clients');
    }
}

