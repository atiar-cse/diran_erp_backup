<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayrollBonusSummeriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payroll_bonus_summeries', function (Blueprint $table) {
            $table->increments('id');
            $table->date('payroll_bonus_month');

            $table->decimal('payable_total_bonus',18,2);

            $table->integer('debit_account_id')->unsigned();
            $table->integer('credit_account_id')->unsigned();
            $table->tinyInteger('approve_status')->default('0');
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
        Schema::dropIfExists('payroll_bonus_summeries');
    }
}
