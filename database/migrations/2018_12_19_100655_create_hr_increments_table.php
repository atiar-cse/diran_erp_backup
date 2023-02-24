<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHrIncrementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hr_increments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('employee_id')->unsigned();
            $table->integer('current_department_id')->unsigned();
            $table->integer('current_designation_id')->unsigned();
            $table->integer('current_pay_grade_id')->unsigned();
            $table->integer('current_salary');
            $table->integer('promoted_pay_grade')->unsigned();
            $table->integer('new_salary');
            $table->date('increment_date');
            $table->text('narration')->nullable();
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->tinyInteger('status')->default('1');
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
        Schema::dropIfExists('hr_increments');
    }
}
