<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventoryStockTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_stock_transfers', function (Blueprint $table) {
            $table->increments('id');
			$table->string('transfers_no')->unique();
            $table->date('transfers_date')->nullable();
            $table->integer('from_warehouse_id')->unsigned();
			$table->integer('to_warehouse_id')->unsigned();
            $table->string('transfers_narration',255)->nullable();
            $table->decimal('transfers_total_qty',18,2);
            $table->decimal('transfers_total_price',18,2);
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
        Schema::dropIfExists('inventory_stock_transfers');
    }
}
