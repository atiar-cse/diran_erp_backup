<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductionKilnDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('production_kiln_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('kiln_section_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->string('remarks')->nullable();
            $table->decimal('kiln_weight',18,2);
            $table->decimal('current_qty',13,2);
            $table->decimal('receive_qty',13,2);
            $table->decimal('remain_qty',13,2);
            $table->decimal('trans_to_hv_qty',18,2);
            $table->decimal('trans_to_hv_weight',18,2);
            $table->decimal('reject_qty',18,2);
            $table->decimal('reject_weight',18,2);
            $table->decimal('unit_price',18,2);
            $table->decimal('overhead_price',18,2);
            $table->decimal('total_price',18,2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('production_kiln_details');
    }
}
