<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_infos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('phone')->nullable();
            $table->integer('mobile')->nullable();
            $table->string('email',50)->unique()->nullable();
            $table->string('fax')->nullable();
            $table->string('web')->nullable();
            $table->text('address');
            $table->text('address2');
            $table->string('logo');
            $table->tinyInteger('status')->default('1')->nullable();
            $table->integer('created_by')->nullable();
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
        Schema::dropIfExists('company_infos');
    }
}
