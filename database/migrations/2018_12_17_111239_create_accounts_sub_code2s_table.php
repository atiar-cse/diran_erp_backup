<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsSubCode2sTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts_sub_code2s', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('main_code_id')->unsigned()->nullable();
            $table->integer('sub_code_id')->unsigned();
            $table->string('sub_code_title2')->unique();
            $table->integer('sub_code2')->unique()->nullable();
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
        Schema::dropIfExists('accounts_sub_code2s');
    }
}
