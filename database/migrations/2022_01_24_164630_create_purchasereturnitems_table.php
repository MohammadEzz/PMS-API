<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasereturnitemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchasereturnitems', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchasereturnbill_id')->references('id')->on('purchasereturnbills')->cascadeOnDelete();
            $table->foreignId('purchaseitem_id')->references('id')->on('purchaseitems');
            $table->float('quantity', 8, 2);
            $table->decimal('price', 10, 3);
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
        Schema::dropIfExists('purchasereturnitems');
    }
}
