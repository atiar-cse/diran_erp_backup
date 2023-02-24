<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsContraEntryVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts_contra_entry_vouchers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('contra_entry_no')->unique();
            $table->date('contra_date');

			$table->enum('type', ['customer', 'supplier', 'employee', 'lc'])->nullable();
			$table->integer('type_id')->unsigned()->nullable();
            $table->string('type_desc')->nullable();

            $table->tinyInteger('payment_method');
            $table->integer('party_id')->unsigned()->nullable();
            $table->integer('cost_center_id')->unsigned()->nullable();
            $table->integer('branch_id')->unsigned()->nullable();
            $table->decimal('amount',18,2);
            $table->integer('debit_account_id')->unsigned();
            $table->text('debit_remarks')->nullable();
            $table->integer('credit_account_id')->unsigned();
            $table->text('credit_remarks')->nullable();
            
            $table->integer('bank_id')->unsigned()->nullable();
            $table->date('cheque_date')->nullable();
            $table->string('account_name')->nullable();
            $table->string('account_no')->nullable();
            $table->string('cheque_book')->nullable();
            $table->string('cheque_leaf')->nullable();
            $table->string('cheque_remarks')->nullable();
            $table->text('narration')->nullable();
            $table->integer('voucher_type_id')->nullable();
            $table->integer('voucher_ref_id')->nullable();
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
        Schema::dropIfExists('accounts_contra_entry_vouchers');
    }
}
