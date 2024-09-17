<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('demandes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->decimal('total_amount', 10, 2);
            $table->text('description')->nullable();
            $table->string('status')->default('en_cours');  // New status column with default value
            $table->timestamps();
        });

        // Table for the many-to-many relationship between demandes and articles
        Schema::create('demande_article', function (Blueprint $table) {
            $table->foreignId('demande_id')->constrained('demandes')->onDelete('cascade');
            $table->foreignId('article_id')->constrained('articles')->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->primary(['demande_id', 'article_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('demande_article');
        Schema::dropIfExists('demandes');
    }
};
