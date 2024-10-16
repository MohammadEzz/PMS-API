<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDrugalternativesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drugalternatives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('drug_id')->nullable(false)->references('id')->on('drugs')->cascadeOnDelete();
            $table->foreignId('alternative_id')->nullable(false)->references('id')->on('drugs')->cascadeOnDelete();
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
        Schema::dropIfExists('drugalternatives');
    }
}
