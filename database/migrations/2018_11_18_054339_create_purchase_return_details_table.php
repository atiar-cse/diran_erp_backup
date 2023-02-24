<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseReturnDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_return_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('po_rtnd_return_id')->unsigned();
            $table->integer('po_rtnd_product_id')->unsigned();
            $table->string('po_rtnd_remarks')->nullable();
            $table->string('po_rtnd_messure_unit')->nullable();
            $table->decimal('po_rtnd_unit_price',18,2)->nullable();
            $table->decimal('po_rtnd_qty',18,2)->nullable();
            $table->decimal('po_rtnd_total_price',18,2)->nullable();
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
        Schema::dropIfExists('purchase_return_details');
    }
}
