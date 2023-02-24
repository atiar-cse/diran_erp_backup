<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrTeamsTable extends Migration
{
    public function up()
    {
        Schema::create('hr_teams', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('dept_id')->unsigned();
            $table->string('title');
            $table->integer('head_person')->unsigned()->nullable();
            $table->tinyInteger('status')->default('1');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hr_teams');
    }
}
