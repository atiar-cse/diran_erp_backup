<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHrApplyForLeavesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hr_apply_for_leaves', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('employee_id')->unsigned();
            $table->integer('leave_type_id')->unsigned();
            //$table->string('current_balance',255);
            $table->date('application_from_date')->nullable();
            $table->date('application_to_date')->nullable();
            $table->string('number_of_day',255);
            $table->string('purpose',255)->nullable();
            $table->tinyInteger('check_status')->default('0')->comment = "check_status(0,1) = Unchecked,checked";
            $table->tinyInteger('status')->default('1')->comment = "status(1,2,3) = Pending,Approve,Reject";
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
        Schema::dropIfExists('hr_apply_for_leaves');
    }
}
