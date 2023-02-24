<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLcOthersChargeDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lc_others_charge_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lc_other_charge_entry_id')->unsigned();
            $table->integer('lc_cost_name_id')->unsigned();
            $table->decimal('cost_value',18,2)->comment('in BDT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lc_others_charge_details');
    }
}
