<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('iso', 5);
            $table->string('name', 50);
            $table->string('nicename', 50);
            $table->string('arnicename', 50)->nullable();
            $table->string('iso3', 5)->nullable();
            $table->smallInteger('numcode')->nullable();
            $table->smallInteger('phonecode');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('countries');
    }
}
