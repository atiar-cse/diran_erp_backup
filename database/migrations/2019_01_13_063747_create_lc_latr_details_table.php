<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLcLatrDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lc_latr_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lc_latr_payment_entry_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->string('remarks',255)->nullable();
            $table->string('hs_code',100)->nullable();
            $table->decimal('unit_price',18,2)->comment('in BDT');
            $table->integer('qty');
            $table->integer('measure_unit_id')->unsigned();
            $table->decimal('total_amount',18,2)->comment('in BDT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lc_latr_details');
    }
}
