<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActiveingredientDiseaseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('disease_activeingredient', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disease_id')->nullable(false)->references('id')->on('diseases')->cascadeOnDelete();
            $table->foreignId('activeingredient_id')->nullable(false)->references('id')->on('activeingredients')->cascadeOnDelete();
            $table->tinyInteger('order')->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('disease_activeingredient');
    }
}
