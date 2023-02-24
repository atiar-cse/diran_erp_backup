<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrBonusTable extends Migration
{
    public function up()
    {
        Schema::create('hr_bonus', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->unique();
            $table->integer('unit_id')->unsigned();
            $table->integer('employee_type')->unsigned();
            $table->integer('start_month')->unsigned();
            $table->integer('end_month')->unsigned();
            $table->enum('salary_type', ['Basic', 'Gross']);
            $table->decimal('amount_percent', 18, 2);
            $table->integer('bonus_percent')->unsigned();
            $table->date('effective_date')->nullable();
            $table->tinyInteger('status')->default('1');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hr_bonus');
    }
}
