<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('bill_id')->unsigned();
            $table->string('bill_type', 255);
            $table->decimal('amountpaid', 10, 3);
            $table->decimal('amountunpaid', 10, 3);
            $table->enum('paymentmethod', ['cash', 'cheque', 'moneytransfer']);
            $table->string('chequenum', 225)->nullable();
            $table->string('transfernum', 225)->nullable();
            $table->date('paiddate');
            $table->boolean('editable');
            $table->text('note')->nullable();
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
        Schema::dropIfExists('payments');
    }
}
