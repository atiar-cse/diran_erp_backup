<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCheckBookLeafsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('check_book_leafs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('chart_ac_id')->unsigned();
            $table->integer('account_no')->nullable();
            $table->integer('book_no')->nullable();
            $table->string('prefix')->nullable();
            $table->string('suffix')->nullable();
            $table->decimal('check_start',18,0);
            $table->integer('check_range');
            $table->integer('cheque_no_length')->nullable();
            $table->date('check_gen_date')->nullable();
            $table->text('details')->nullable();

            $table->tinyInteger('approve_status')->default('0');


            $table->tinyInteger('status')->default('1');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('approve_by')->nullable();
            $table->dateTime('approve_at')->nullable();
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
        Schema::dropIfExists('check_book_leafs');
    }
}
