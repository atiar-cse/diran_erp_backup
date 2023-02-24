<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsBankPaymentDetailsVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts_bank_payment_details_vouchers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('bank_payment_id')->unsigned();
            $table->integer('type_id')->unsigned()->nullable();
            $table->string('type_desc')->nullable();
            $table->integer('debit_account_id')->unsigned()->nullable();
            $table->integer('credit_account_id')->unsigned()->nullable();
            $table->integer('sub_code2_id')->unsigned()->nullable();
            $table->string('remarks')->nullable();
            $table->string('check_book')->nullable();
            $table->string('check_leaf')->nullable();

            $table->date('check_date')->nullable();
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
        Schema::dropIfExists('accounts_bank_payment_details_vouchers');
    }
}
