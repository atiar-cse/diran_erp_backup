<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductionIndirectCostDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('production_indirect_cost_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('indir_cost_id')->unsigned();
            $table->integer('prod_indir_costs_type_id')->unsigned();
            $table->string('remarks')->nullable();
            $table->decimal('amount',13,2);            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('production_indirect_cost_details');
    }
}