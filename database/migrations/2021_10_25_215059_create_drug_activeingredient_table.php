<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDrugActiveingredientTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drug_activeingredient', function (Blueprint $table) {
            $table->id();
            $table->foreignId('drug_id')->nullable(false)->references('id')->on('drugs')->cascadeOnDelete();
            $table->foreignId('activeingredient_id')->nullable(false)->references('id')->on('activeingredients')->cascadeOnDelete();
            $table->float('concentration')->nullable();
            $table->bigInteger('format')->nullable();
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
        Schema::dropIfExists('drug_activeingredient');
    }
}
