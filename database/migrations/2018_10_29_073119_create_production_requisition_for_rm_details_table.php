<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductionRequisitionForRmDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('production_requisition_for_rm_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('requisition_for_rm_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->decimal('current_stock_qty',18,2);
            $table->decimal('qty',18,2);

            $table->decimal('unit_price',18,2)->nullable();
            $table->decimal('total_price',18,2)->nullable();

            $table->string('remarks')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('production_requisition_for_rm_details');
    }
}
