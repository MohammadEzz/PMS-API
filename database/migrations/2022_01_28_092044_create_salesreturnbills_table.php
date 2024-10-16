<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesreturnbillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salesreturnbills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salesbill_id')->nullable()->references('id')->on('salesbills');
            $table->float('discount', 6, 3);
            $table->decimal('total', 10, 3);
            $table->decimal('paymentamount', 10, 3);
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
        Schema::dropIfExists('salesreturnbills');
    }
}
