<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseRequisitionDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_requisition_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pr_requisition_id')->unsigned();
            $table->integer('pr_product_id')->unsigned();
            $table->decimal('pr_qty',18,2);
            $table->string('pr_unit');
            $table->decimal('pr_unit_price',18,2)->nullable();
            $table->decimal('pr_total_price',18,2)->nullable();
            $table->string('pr_remarks')->nullable();
            $table->enum('product_type', ['Local', 'Import']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_requisition_details');
    }
}
