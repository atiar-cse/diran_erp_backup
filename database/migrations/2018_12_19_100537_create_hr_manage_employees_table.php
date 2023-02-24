<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHrManageEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hr_manage_employees', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('role_id')->unsigned()->nullable();
            $table->string('user_name')->unique()->nullable();
            $table->string('password')->nullable();
            $table->string('first_name',50);
            $table->string('last_name',50)->nullable();
            $table->string('emp_id',191)->nullable();
            $table->string('fingerprint_no')->unique()->nullable();
            $table->string('supervisor')->nullable();
            $table->integer('department_id')->unsigned();
            $table->integer('designation_id')->unsigned();
            $table->integer('branch_id')->unsigned()->nullable();
            $table->integer('work_shift_id')->unsigned();
            $table->integer('monthly_pay_grade_id')->unsigned()->nullable();
            $table->integer('hourly_pay_grade_id')->unsigned()->nullable();
            $table->integer('employee_type_id')->unsigned()->nullable();
            $table->integer('team_id')->unsigned()->nullable();
            $table->integer('salary_grade_id')->unsigned()->nullable();
            $table->float('gross_salary',10,2)->nullable();
            $table->float('basic_salary',10,2)->nullable();
            $table->float('house_rent',10,2)->nullable();
            $table->float('medical',10,2)->nullable();
            $table->float('transport',10,2)->nullable();
            $table->float('lunch',10,2)->nullable();
            $table->float('compliance_salary',10,2)->nullable();
            $table->float('comp_basic_salary',10,2)->nullable();
            $table->float('comp_house_rent',10,2)->nullable();
            $table->float('comp_medical',10,2)->nullable();
            $table->float('comp_transport',10,2)->nullable();
            $table->float('comp_lunch',10,2)->nullable();
            $table->string('email',50)->unique()->nullable();
            $table->integer('phone');
            $table->string('gender',10);
            $table->integer('religion_id')->unsigned()->nullable();
            $table->integer('report_two_id')->nullable();
            $table->date('date_of_birth');
            $table->date('date_of_joining');
            $table->date('date_of_leaving')->nullable();
            $table->string('marital_status',10)->nullable();
            $table->string('emergency_contact')->nullable();
            $table->string('photo',250)->nullable();
            $table->text('em_address')->nullable();
            $table->tinyInteger('status')->default('1');
            $table->tinyInteger('permanent_status')->default(0);
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
        Schema::dropIfExists('hr_manage_employees');
    }
}
