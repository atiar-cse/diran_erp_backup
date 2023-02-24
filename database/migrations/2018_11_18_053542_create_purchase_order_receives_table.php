<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseOrderReceivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_order_receives', function (Blueprint $table) {
            $table->increments('id');
            $table->string('po_order_no')->unique();
			$table->integer('po_warehouse_id')->unsigned();
            $table->integer('po_supplier_id')->unsigned();
            $table->integer('po_requisition_id')->unsigned();
            $table->date('po_receive_date')->nullable();

            $table->string('po_narration',255)->nullable();
            $table->binary('po_order_docs')->nullable();
            $table->decimal('po_total_qty',18,2);
            $table->decimal('po_total_price',18,2);

			$table->tinyInteger('approve_status')->default('0');
			//$table->tinyInteger('stock_enrty_status')->default('0');
			$table->tinyInteger('account_status')->default('0');

            $table->tinyInteger('status')->default('1');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('approve_by')->nullable();
            $table->dateTime('approve_at')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('purchase_order_receives');
    }
}
