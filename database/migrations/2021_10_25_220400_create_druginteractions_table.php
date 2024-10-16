<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDruginteractionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('druginteractions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activeingredient1')->nullable(false)->references('id')->on('activeingredients')->cascadeOnDelete();
            $table->foreignId('activeingredient2')->nullable(false)->references('id')->on('activeingredients')->cascadeOnDelete();
            $table->bigInteger('level')->nullable(false);
            $table->text('description')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('druginteractions');
    }
}
