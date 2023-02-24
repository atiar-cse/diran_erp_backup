<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductionAssemblingDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('production_assembling_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('assembling_section_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->string('remarks')->nullable();
            $table->decimal('current_qty',13,2);
            $table->decimal('receive_qty',13,2);
            // $table->decimal('adj_qty',13,2);

            $table->decimal('trans_to_packing_qty',13,2);
            $table->decimal('unit_price',18,2);
            $table->decimal('overhead_price',18,2);
            $table->decimal('total_price',18,2);
            $table->decimal('remain_qty',13,2);
            // $table->decimal('reject_qty',13,2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('production_assembling_details');
    }
}
