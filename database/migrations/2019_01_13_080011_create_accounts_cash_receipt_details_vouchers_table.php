<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsCashReceiptDetailsVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts_cash_receipt_details_vouchers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cash_receipt_id')->unsigned();
            $table->integer('debit_account_id')->unsigned()->nullable();
            $table->integer('credit_account_id')->unsigned()->nullable();
            $table->integer('sub_code2_id')->unsigned()->nullable();
            $table->string('remarks')->nullable();
            $table->integer('type_id')->unsigned()->nullable();
            $table->string('type_desc')->nullable();
            $table->string('money_receipt_no')->nullable();
            $table->decimal('debit_amount',18,2)->default(0);
            $table->decimal('credit_amount',18,2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounts_cash_receipt_details_vouchers');
    }
}
