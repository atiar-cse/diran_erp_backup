<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStockTransectionLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_transection_logs', function (Blueprint $table) {

            $table->increments('id');

            $table->date('stock_transection_logs_date');
            $table->tinyInteger('is_in_out')->default(0)->comment('1=Stock In,2=Stock Out');
            $table->integer('stock_transection_logs_type');//0-pr-entry, 1-pr-return,2-delivery-challan, 3 -delicery return ->nullable();
            $table->string('stock_transection_logs_number');

            $table->string('stock_transection_logs_ref_table_name')->nullable();
            $table->integer('stock_transection_logs_ref_table_id')->nullable();

            $table->string('stock_transection_logs_table_name');
            $table->integer('stock_transection_logs_table_id')->unsigned();


            $table->string('stock_trans_supp_cus_table_name')->nullable();
            $table->integer('stock_trans_supp_cus_table_id')->unsigned()->nullable();
            $table->string('stock_trans_tender')->nullable();

            $table->integer('stock_trans_warehouse_id')->nullable();

            $table->decimal('stock_trans_qty',18,2)->nullable();
            $table->decimal('stock_trans_total_price',18,2)->nullable();

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
        Schema::dropIfExists('stock_transection_logs');
    }
}
