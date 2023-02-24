<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsPurchaseReturnVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts_purchase_return_vouchers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('vouchers_no')->unique();
            $table->integer('purchase_return_id')->unsigned();
            $table->date('purchase_return_date');
            $table->integer('supplier_id')->unsigned();

            $table->integer('dr_chart_of_account_id')->unsigned()->nullable();
            $table->integer('cr_chart_of_account_id')->unsigned()->nullable();
            $table->integer('cost_center_id')->unsigned()->nullable();
            $table->string('narration')->nullable();


            $table->tinyInteger('payment_type')->nullable();
			$table->integer('bank_id')->unsigned()->nullable();
            $table->date('cheque_date')->nullable();
            $table->string('account_name')->nullable();
            $table->string('account_no')->nullable();
            $table->string('cheque_book')->nullable();
            $table->string('cheque_leaf')->nullable();
            $table->string('cheque_remarks')->nullable();

            $table->decimal('total_qty',18,2);
            $table->decimal('total_price',18,2);


            $table->decimal('price_paid',18,2)->nullable();
            $table->decimal('price_due',18,2)->nullable();


            $table->tinyInteger('approve_status')->default('0');

            $table->tinyInteger('status')->default('1');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('approve_by')->nullable();
            $table->dateTime('approve_at')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('accounts_purchase_return_vouchers');
    }
}
