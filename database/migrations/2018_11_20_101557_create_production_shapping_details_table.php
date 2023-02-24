<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductionShappingDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('production_shapping_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shapping_section_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->string('remarks')->nullable();
            $table->decimal('shapping_product_weight',18,2);
            $table->decimal('current_qty',18,2);
            $table->decimal('receive_qty',18,2);
			$table->decimal('remain_qty',18,2);
            $table->decimal('trans_to_dry_qty',18,2);
            $table->decimal('trans_to_dry_weight',18,2);

            $table->decimal('dry_westage_qty',18,2);
            $table->decimal('dry_westage_weight',18,2);

            $table->decimal('unit_price',13,2);
            $table->decimal('overhead_price',13,2);
            $table->decimal('total_price',13,2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('production_shapping_details');
    }
}
