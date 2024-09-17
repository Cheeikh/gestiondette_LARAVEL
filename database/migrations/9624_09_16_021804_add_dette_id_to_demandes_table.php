<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('demandes', function (Blueprint $table) {
            $table->unsignedBigInteger('dette_id')->nullable()->after('status');
            $table->foreign('dette_id')->references('id')->on('dettes')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('demandes', function (Blueprint $table) {
            $table->dropForeign(['dette_id']);
            $table->dropColumn('dette_id');
        });
    }

};
