<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductionFormingDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('production_forming_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('forming_section_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->string('remarks')->nullable();
            $table->decimal('roll_weight',18,2);
            $table->decimal('current_qty')->comment('Opening Qty',18,2);
            $table->decimal('receive_qty')->comment('Production Qty',18,2);
            $table->decimal('trans_to_shapping_qty',18,2);
            $table->decimal('trans_to_shapping_weight',18,2);
			$table->decimal('forming_remain_qty',18,2);
            $table->decimal('shapping_westage_qty',18,2);
            $table->decimal('shapping_westage_weight',18,2);
            $table->decimal('unit_price',13,2);
            $table->decimal('total_price',13,2);
            $table->decimal('overhead_price',13,2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('production_forming_details');
    }
}
