<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLcCommercialInvoiceDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lc_commercial_invoice_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lc_commercial_invoice_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->string('hs_code',100)->nullable();

            $table->decimal('unit_price',18,2)->comment('in BDT');
            $table->decimal('qty',18,2);
            $table->integer('measure_unit_id')->unsigned();
            $table->decimal('gross_weight',18,2)->nullable();
            $table->decimal('net_weight',18,2)->nullable();
            $table->decimal('total_amount',18,2)->comment('in BDT');
            $table->decimal('landed_unit_price',18,2)->comment('in BDT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lc_commercial_invoice_details');
    }
}
