<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductionStockMonthEndsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('production_stock_month_ends', function (Blueprint $table) {
            $table->increments('id');
            $table->date('stock_end_date')->nullable();
            $table->integer('product_id')->unsigned()->nullable();

            $table->decimal('forming_stock_qty',18,2)->default('0')->nullable();
            $table->decimal('shapping_stock_qty',18,2)->default('0')->nullable();
            $table->decimal('dryer_stock_qty',18,2)->default('0')->nullable();
            $table->decimal('glaze_stock_qty',18,2)->default('0')->nullable();
            $table->decimal('kiln_stock_qty',18,2)->default('0')->nullable();
            $table->decimal('testing_stock_qty',18,2)->default('0')->nullable();
            $table->decimal('assembling_stock_qty',18,2)->default('0')->nullable();
            $table->decimal('packing_stock_qty',18,2)->default('0')->nullable();
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
        Schema::dropIfExists('production_stock_month_ends');
    }
}
