<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasebillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchasebills', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('supplier_id');
            $table->bigInteger('dealer_id');
            $table->bigInteger('billnumber');
            $table->date('issuedate');
            $table->boolean('editable');
            $table->enum('paymenttype', ['prepaid', 'postpaid']);
            $table->enum('paidstatus', ['paid', 'unpaid', 'partialpaid']);
            $table->enum('billstatus', ['approved', 'underreview']);
            $table->decimal('total', 10, 3);
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
        Schema::dropIfExists('purchasebills');
    }
}
