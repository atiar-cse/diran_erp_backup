<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventoryStockAdjustDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_stock_adjust_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('inventory_stock_adjust_id')->unsigned();
            $table->integer('inventory_stock_adjust_details_product_id')->unsigned();
            $table->string('inventory_stock_adjust_details_remarks')->nullable();
            $table->enum('inventory_stock_adjust_rule', ['Inc', 'Dec']);
            $table->string('inventory_stock_adjust_details_unit');
            //$table->decimal('inventory_stock_adjust_details_unit_price',18,2);
            $table->decimal('inventory_stock_adjust_details_qty',18,2);
            //$table->decimal('inventory_stock_adjust_details_total_price',18,2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventory_stock_adjust_details');
    }
}
