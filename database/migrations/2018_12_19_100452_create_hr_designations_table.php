<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrDesignationsTable extends Migration
{
    public function up()
    {
        Schema::create('hr_designations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('designation_name')->unique();
            $table->integer('employee_level_id')->unsigned();
            $table->tinyInteger('status')->default('1');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
			
        });
    }

    public function down()
    {
        Schema::dropIfExists('hr_designations');
    }
}
