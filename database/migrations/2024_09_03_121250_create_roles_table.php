<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration  // Renommez la classe ici
{
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // Si vous insérez des rôles par défaut ici, vous pouvez le faire après avoir créé la table
        DB::table('roles')->insert([
            ['name' => 'Admin'],
            ['name' => 'Vendeur'],
            ['name' => 'Client'],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('roles');
    }
}
