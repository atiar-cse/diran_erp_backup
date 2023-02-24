<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventoryProductDamageDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_product_damage_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('inventory_product_damage_id')->unsigned();
            $table->integer('inventory_product_damage_details_product_id')->unsigned();
            $table->string('inventory_product_damage_details_remarks')->nullable();
            $table->string('inventory_product_damage_details_unit');
			$table->decimal('inventory_product_damage_details_qty');
			
            /*$table->decimal('inventory_product_damage_details_unit_price');           
            $table->decimal('inventory_product_damage_details_total_price');*/
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventory_product_damage_details');
    }
}
