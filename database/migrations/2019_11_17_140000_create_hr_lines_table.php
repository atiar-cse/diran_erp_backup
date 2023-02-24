<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrLinesTable extends Migration
{
    public function up()
    {
        Schema::create('hr_lines', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('unit_id')->unsigned();
            $table->string('line_name');
            $table->tinyInteger('status')->default('1');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hr_lines');
    }
}
