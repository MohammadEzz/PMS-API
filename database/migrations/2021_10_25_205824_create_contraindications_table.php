<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContraindicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contraindications', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('category')->nullable(false);
            $table->text('description')->nullable(false);
            $table->bigInteger('level')->nullable(false);
            $table->tinyInteger('order')->nullable(false);
            $table->foreignId('drug_id')->references('id')->on('drugs')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contraindications');
    }
}
