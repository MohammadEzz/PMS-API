<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasereturnbillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchasereturnbills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchasebill_id')->references('id')->on('purchasebills');
            $table->dateTime('issuedate');
            $table->decimal('total', 10, 3);
            $table->boolean('editable');
            $table->enum('billstatus', ['approved', 'underreview']);
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
        Schema::dropIfExists('purchasereturnbills');
    }
}
