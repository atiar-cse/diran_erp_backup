<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayrollMonthlyPayGradesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payroll_monthly_pay_grades', function (Blueprint $table) {
            $table->increments('id');
            $table->string('pay_grade_name',150)->unique();
            $table->integer('gross_salary');
            $table->integer('percentage_of_basic');
            $table->integer('basic_salary');
            $table->integer('over_time_rate')->nullable();
            $table->tinyInteger('status')->default('1');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('payroll_monthly_pay_grades');
    }
}
