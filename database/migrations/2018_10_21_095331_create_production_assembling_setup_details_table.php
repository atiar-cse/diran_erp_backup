<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductionAssemblingSetupDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('production_assembling_setup_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('production_assembling_setup_id')->unsigned();
            $table->integer('fitting_product_id')->unsigned();
            $table->integer('fitting_product_qty');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('production_assembling_setup_details');
    }
}
