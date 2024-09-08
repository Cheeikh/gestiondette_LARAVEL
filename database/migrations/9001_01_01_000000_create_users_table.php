<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            $table->string('login')->unique();
            $table->string('email')->unique(); // Ajout de l'email unique
            $table->string('password');
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->string('photo_cloud')->nullable();  // Lien vers la photo sur Cloudinary
            $table->string('photo_local')->nullable();  // Lien vers la photo en stockage local
            $table->boolean('active')->default(true);
            $table->rememberToken(); // Gestion des sessions persistantes
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
