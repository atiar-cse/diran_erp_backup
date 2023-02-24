<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayrollBonusProcessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payroll_bonus_processes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('bonus_setting_id')->unsigned();
            $table->integer('employee_id')->unsigned();
            $table->string('bonus_month');
            $table->string('festival_name');
            $table->string('bonus_type');
            $table->integer('gross_salary');
            $table->integer('basic_salary');
            $table->integer('percentage_of_bonus');
            $table->integer('bonus_amount');
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
        Schema::dropIfExists('payroll_bonus_processes');
    }
}
