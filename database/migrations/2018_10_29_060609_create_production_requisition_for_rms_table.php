<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductionRequisitionForRmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('production_requisition_for_rms', function (Blueprint $table) {
            $table->increments('id');
            $table->string('rm_requisition_no')->unique();
            $table->date('requisition_date');
            $table->integer('warehouse_id')->unsigned();
            $table->text('narration')->nullable();
            $table->integer('estimate_qty_for_production')->nullable();
            $table->string('requisition_types');
            $table->decimal('total_qty',18,2);
            $table->decimal('total_amount',18,2);
            $table->tinyInteger('approve_status')->default('0');
			$table->tinyInteger('account_status')->default('0');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
			$table->integer('acc_approve_by')->nullable();
            $table->dateTime('acc_approve_at')->nullable();
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
        Schema::dropIfExists('production_requisition_for_rms');
    }
}
