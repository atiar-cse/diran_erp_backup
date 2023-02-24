<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventoryStockTransferDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_stock_transfer_details', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('transfer_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->string('remarks')->nullable();
            $table->string('unit');
            $table->decimal('unit_price',18,2);
            $table->decimal('qty',18,2);
            $table->decimal('total_price',18,2);

            // $table->foreign('unit')->references('id')->on('production_measure_units')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventory_stock_transfer_details');
    }
}
