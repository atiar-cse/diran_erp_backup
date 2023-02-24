<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayrollSalaryProcessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payroll_salary_processes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('employee_id')->unsigned();
            $table->integer('department_id')->unsigned();
            $table->integer('designation_id')->unsigned();
            $table->integer('gross_salary')->default('0');
            $table->integer('basic_salary')->default('0');
            $table->string('monthly_pay_grade_name');
            $table->integer('hourly_pay_rate')->default('0');
            $table->integer('total_working_days')->default('0');
            $table->integer('total_holidays')->default('0');
            $table->integer('total_weekends')->default('0');
            $table->integer('total_number_of_leave')->default('0');
            $table->integer('pay_days')->default('0');
            $table->date('joining_date');
            $table->integer('emp_status')->default('0');
            $table->date('salary_month');
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
        Schema::dropIfExists('payroll_salary_processes');
    }
}
