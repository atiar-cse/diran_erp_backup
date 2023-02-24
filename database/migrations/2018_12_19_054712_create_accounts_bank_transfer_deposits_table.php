<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsBankTransferDepositsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts_bank_transfer_deposits', function (Blueprint $table) {
            $table->increments('id');
            $table->string('bank_transfer_no')->unique();
            $table->date('bank_transfer_date');
			
			$table->enum('type', ['customer', 'supplier', 'employee', 'lc'])->nullable();
			$table->integer('type_id')->unsigned()->nullable();
            $table->string('type_desc')->nullable();
			
            $table->tinyInteger('payment_method');
            $table->integer('branch_id')->unsigned()->nullable();
            $table->decimal('amount',18,2);
            $table->integer('debit_account_id')->unsigned();
            $table->integer('credit_account_id')->unsigned();
            $table->integer('bank_id')->unsigned()->nullable();
            $table->date('cheque_date')->nullable();
            $table->string('account_name')->nullable();
            $table->string('account_no')->nullable();
            $table->string('cheque_book')->nullable();
            $table->string('cheque_leaf')->nullable();
            $table->string('cheque_remarks')->nullable();
            $table->text('narration')->nullable();
            $table->tinyInteger('approve_status')->default('0');
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
        Schema::dropIfExists('accounts_bank_transfer_deposits');
    }
}
