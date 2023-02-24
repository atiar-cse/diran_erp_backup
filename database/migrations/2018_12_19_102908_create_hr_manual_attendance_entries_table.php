<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHrManualAttendanceEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hr_manual_attendance_entries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('employee_attendance_employee_id')->unsigned();
            $table->integer('employee_attendance_department_id')->unsigned();
            $table->integer('employee_shift_id')->unsigned();
            $table->date('emp_attendance_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->tinyInteger('status')->default('0')->comment= "status(0,1,2) = Present,Leave,Absent";
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('hr_manual_attendance_entries');
    }
}
