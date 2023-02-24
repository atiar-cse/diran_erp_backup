<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseOrderReceiveDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_order_receive_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pod_order_id')->unsigned();
            $table->integer('pod_product_id')->unsigned();
            $table->string('pod_remarks')->nullable();
            $table->string('pod_unit')->nullable();
            $table->decimal('pod_unit_price',18,2)->nullable();
            $table->decimal('pod_qty',18,2)->nullable();
            $table->decimal('pod_total_price',18,2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_order_receive_details');
    }
}
