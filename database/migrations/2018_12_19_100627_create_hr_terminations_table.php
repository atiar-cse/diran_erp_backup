<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHrTerminationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hr_terminations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('employee_terminated_employee_id')->unsigned();
            $table->string('termination_types');
            $table->string('subject');
            $table->integer('terminated_by_employee_id')->unsigned();
            $table->date('notice_date');
            $table->date('termination_date');
            $table->text('narration')->nullable();
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
        Schema::dropIfExists('hr_terminations');
    }
}
