<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLcCustomDutyChargeDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lc_custom_duty_charge_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lc_custom_duty_entry_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->decimal('cost_value',13,2)->comment('in BDT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lc_custom_duty_charge_details');
    }
}
