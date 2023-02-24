<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductionFinishedStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('production_finished_stocks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('finished_stock_no')->unique();
            $table->date('date');
            $table->integer('warehouse_id')->unsigned()->nullable();
            $table->text('narration')->nullable();
            $table->decimal('total_receive_qty',18,2);
            $table->decimal('total_adj_qty',18,2);
            $table->decimal('total_trans_to_store_qty',18,2);
            $table->decimal('total_amount',18,2);
            $table->decimal('total_remain_qty',18,2);
            $table->decimal('total_reject_qty',18,2);

            $table->text('overhead_info')->nullable();
            $table->decimal('reject_overhead_amt',13,2)->default(0);
            $table->decimal('reject_amt',13,2)->default(0);

            $table->tinyInteger('status')->default('1');
            $table->tinyInteger('approve_status')->default('0');
            $table->tinyInteger('account_status')->default('0');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('approve_by')->nullable();
            $table->integer('acc_approve_by')->nullable();
            $table->dateTime('approve_at')->nullable();
            $table->dateTime('acc_approve_at')->nullable();
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
        Schema::dropIfExists('production_finished_stocks');
    }
}
