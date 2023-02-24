<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHrWarningsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hr_warnings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('warning_to_employee_id')->unsigned();
            $table->string('warning_types');
            $table->string('subject');
            $table->integer('warning_by_employee_id')->unsigned();
            $table->date('warning_date');
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
        Schema::dropIfExists('hr_warnings');
    }
}
