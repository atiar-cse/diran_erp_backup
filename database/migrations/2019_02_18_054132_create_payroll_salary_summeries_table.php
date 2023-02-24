<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayrollSalarySummeriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payroll_salary_summeries', function (Blueprint $table) {
            $table->increments('id');
            $table->date('payroll_salary_month');
            $table->decimal('payable_total_salary',13,2);
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
        Schema::dropIfExists('payroll_salary_summeries');
    }
}
