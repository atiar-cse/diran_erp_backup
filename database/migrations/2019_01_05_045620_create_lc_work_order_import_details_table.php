<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLcWorkOrderImportDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lc_work_order_import_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('work_order_import_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->integer('currency_id')->unsigned();
            $table->string('remarks')->nullable();
            $table->decimal('unit_price',13,3);
            $table->integer('qty');
            $table->decimal('total_price',13,2);
            $table->decimal('bdt_convert_rate',13,2);
            $table->decimal('total_amount_taka',13,2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lc_work_order_import_details');
    }
}
