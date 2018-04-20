<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWineTypesTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wine_types_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_wine_types')->unsigned();
            $table->integer('id_language')->unsigned();
            $table->string('name');

            $table->unique(['id_wine_types','id_language']);

            $table->foreign('id_wine_types')
                ->references('id')->on('wine_types')
                ->onDelete('cascade');

            $table->foreign('id_language')
                ->references('id')->on('languages')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wine_types_translations');
    }
}
