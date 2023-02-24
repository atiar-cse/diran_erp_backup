<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLcProformaInvoiceDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lc_proforma_invoice_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lc_proforma_invoice_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->integer('currency_id')->unsigned();
            $table->string('remarks')->nullable();
            $table->decimal('unit_price',18,2);
            $table->decimal('qty',18,2);
            $table->decimal('total_price',18,2);
            $table->decimal('bdt_convert_rate',18,2);
            $table->decimal('total_amount_taka',18,2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lc_proforma_invoice_details');
    }
}
