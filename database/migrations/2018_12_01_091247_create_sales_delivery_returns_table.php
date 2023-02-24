<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesDeliveryReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_delivery_returns', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sales_delivery_return_no')->unique();
            $table->date('sales_delivery_return_date')->nullable();
            $table->integer('sales_delivery_return_warehouse_id')->unsigned();
            $table->integer('sales_delivery_return_customer_id')->unsigned();
            $table->integer('sales_delivery_return_return_types_id')->unsigned();
            $table->string('sales_delivery_return_narration',255)->nullable();
            $table->decimal('sales_delivery_return_total_qty',18,2);
            $table->decimal('sales_delivery_return_total_price',18,2);
			
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
        Schema::dropIfExists('sales_delivery_returns');
    }
}
