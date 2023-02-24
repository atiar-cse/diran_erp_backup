<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_returns', function (Blueprint $table) {
            $table->increments('id');
            $table->string('po_return_no')->unique();
            $table->date('po_return_date')->nullable();

            $table->integer('po_rtn_supplier_id')->unsigned();
            $table->integer('po_rtn_warehouse_id')->unsigned();
            $table->integer('po_rtn_issue_type_id')->unsigned();

            $table->string('po_rtn_narration',255)->nullable();
            $table->binary('po_rtn_docs')->nullable();

            $table->decimal('po_rtn_total_qty',18,2);
            $table->decimal('po_rtn_total_price',18,2);


			$table->tinyInteger('approve_status')->default('0');
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
        Schema::dropIfExists('purchase_returns');
    }
}
