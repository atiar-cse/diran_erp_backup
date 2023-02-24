<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrShiftScheduleTable extends Migration
{
    public function up()
    {
        Schema::create('hr_shift_schedule', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->unique();
            $table->time('in_start');
            $table->time('in_time');
            $table->time('late_start');
            $table->time('in_end');
            $table->time('out_start');
            $table->time('out_end');
            $table->tinyInteger('status')->default('1');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hr_shift_schedule');
    }
}
