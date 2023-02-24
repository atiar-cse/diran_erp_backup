<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHrSalaryConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hr_salary_configurations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key_name',100)->comment('basic_salary,house_rent,medical,transport');
            $table->string('salary_head',55);
            $table->enum('salary_type', ['Gross', 'Basic']);
            $table->decimal('percentage',5,2);
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
        Schema::dropIfExists('hr_salary_configurations');
    }
}
