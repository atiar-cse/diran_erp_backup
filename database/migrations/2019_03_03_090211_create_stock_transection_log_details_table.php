<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStockTransectionLogDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_transection_log_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('log_id');
            $table->tinyInteger('is_in_out')->default(0)->comment('1=Stock In,2=Stock Out');
            $table->string('log_table_name');
            $table->integer('log_table_id')->unsigned();

            $table->integer('product_id')->unsigned();
            $table->integer('warehouse_id')->unsigned();

            $table->decimal('log_open_qty',18,2)->nullable();
            $table->decimal('log_entry_qty',18,2);
            $table->decimal('log_current_qty',18,2);
            $table->decimal('log_close_qty',18,2)->nullable();

            $table->decimal('log_unit_price',18,2)->nullable();
            $table->decimal('log_total_price',18,2)->nullable();
			
			$table->tinyInteger('add_sub')->default('1');
			
            $table->date('entry_date');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();

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
        Schema::dropIfExists('stock_transection_log_details');
    }
}
