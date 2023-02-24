<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseRequisitionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_requisitions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('purchase_requisition_no')->unique();
            $table->date('purchase_requisition_date')->nullable();
            $table->string('purchase_requisition_narration',255)->nullable();
            $table->string('purchase_requisition_supervisor_narration',255)->nullable();
            $table->decimal('purchase_requisition_total_qty',18,2);
            $table->decimal('purchase_requisition_total_price',18,2);

            $table->tinyInteger('stock_enrty_status')->default('0');
			$table->tinyInteger('approve_status')->default('0');
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
        Schema::dropIfExists('purchase_requisitions');
    }
}
