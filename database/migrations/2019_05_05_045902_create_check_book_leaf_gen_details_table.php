<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCheckBookLeafGenDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('check_book_leaf_gen_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('check_book_leaf_id')->unsigned();
            $table->integer('chart_ac_id')->unsigned();
            $table->string('leaf_number',255);
            $table->tinyInteger('use_status')->default('0')->comment('0=Available to use,1=Used,2=In Draft');
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
        Schema::dropIfExists('check_book_leaf_gen_details');
    }
}
