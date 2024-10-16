<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseitemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchaseitems', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchasebill_id')->references('id')->on('purchasebills')->cascadeOnDelete();
            $table->foreignId('drug_id')->references('id')->on('drugs');
            $table->float('quantity', 8, 2);
            $table->smallInteger('bonus')->nullable();
            $table->decimal('purchaseprice', 8, 3);
            $table->decimal('sellprice', 8, 3);
            $table->float('tax', 6, 3);
            $table->float('discount', 6, 3);
            $table->date('expiredate');
            $table->foreignId('created_by')->references('id')->on('users');
            $table->foreignId('updated_by')->nullable()->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchaseitems');
    }
}
