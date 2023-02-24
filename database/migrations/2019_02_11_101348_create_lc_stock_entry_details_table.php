<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLcStockEntryDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lc_stock_entry_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lc_stock_entry_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->string('hs_code',100)->nullable();
            $table->decimal('unit_price',13,2)->comment('in BDT');
            $table->integer('qty');
            $table->integer('measure_unit_id')->unsigned();
            $table->decimal('gross_weight',13,2)->nullable();
            $table->decimal('net_weight',13,2)->nullable();
            $table->decimal('total_amount',13,2)->comment('in BDT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lc_stock_entry_details');
    }
}
