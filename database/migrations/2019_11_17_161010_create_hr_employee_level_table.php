<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrEmployeeLevelTable extends Migration
{
    public function up()
    {
        Schema::create('hr_employee_level', function (Blueprint $table) {
            $table->increments('id');
            $table->string('employee_level_name')->unique();
            $table->tinyInteger('order')->unique();
            $table->tinyInteger('status')->default('1');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hr_employee_level');
    }
}
