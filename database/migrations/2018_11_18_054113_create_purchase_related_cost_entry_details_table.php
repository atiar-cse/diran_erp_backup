<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseRelatedCostEntryDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_related_cost_entry_details', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('po_cost_id')->unsigned();
            $table->integer('po_cost_type_id')->unsigned();

            $table->text('remarks')->nullable();
            $table->decimal('po_cost_amount',18,2)->nullable();

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
        Schema::dropIfExists('purchase_related_cost_entry_details');
    }
}
