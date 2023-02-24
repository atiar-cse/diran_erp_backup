<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesDeliveryChallanDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_delivery_challan_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sales_delivery_id')->unsigned();
            $table->integer('sales_delivery_details_product_id')->unsigned();
            $table->string('sales_delivery_details_description')->nullable();
            $table->string('sales_delivery_details_unit');


            $table->decimal('sales_delivery_details_unit_price',18,2);
            $table->string('sales_delivery_details_discount_type');
            $table->decimal('sales_delivery_details_discount',18,2);
            $table->string('sales_delivery_details_vat_sub',18,2);
            $table->decimal('sales_delivery_details_qty',18,2);
            $table->decimal('sales_delivery_details_total_price',18,2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales_delivery_challan_details');
    }
}
