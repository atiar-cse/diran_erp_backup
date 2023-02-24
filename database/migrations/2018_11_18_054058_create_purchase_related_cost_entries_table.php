<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseRelatedCostEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_related_cost_entries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('po_order_receive_id')->unsigned();
            $table->date('po_cost_date')->nullable();
            $table->string('po_cost_narration',255)->nullable();
            $table->decimal('po_total_cost', 18,2);

			$table->integer('credit_ac_id')->unsigned();
            $table->tinyInteger('status')->default('1');
			$table->tinyInteger('approve_status')->default('0');
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
        Schema::dropIfExists('purchase_related_cost_entries');
    }
}
