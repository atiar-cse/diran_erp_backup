<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsCashPaymentVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts_cash_payment_vouchers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('payment_transaction_no')->unique();
            $table->date('payment_date');
            $table->enum('type', ['customer', 'supplier', 'employee', 'lc'])->nullable();
            $table->integer('cost_center_id')->unsigned()->nullable();
            $table->integer('branch_id')->unsigned()->nullable();
            $table->integer('project_id')->unsigned()->nullable();

            $table->decimal('total_debit_amount',18,2);
            $table->decimal('total_credit_amount',18,2);

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
        Schema::dropIfExists('accounts_cash_payment_vouchers');
    }
}
