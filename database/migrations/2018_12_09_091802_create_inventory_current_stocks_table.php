<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventoryCurrentStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_current_stocks', function (Blueprint $table) {

            $table->increments('id');

            $table->integer('inventory_current_stocks_product_id')->unsigned();
            $table->integer('inventory_current_stocks_warehouse_id')->unsigned();

            $table->decimal('inventory_stocks_open_qty',18,2);
            $table->decimal('inventory_stocks_current_qty',18,2);

            $table->decimal('unit_price',18,2)->default('0');
            $table->decimal('total_price',18,2)->default('0');

            $table->tinyInteger('status')->default('1');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();

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
        Schema::dropIfExists('inventory_current_stocks');
    }
}
