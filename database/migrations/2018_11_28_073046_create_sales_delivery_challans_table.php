<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesDeliveryChallansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_delivery_challans', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sales_delivery_no')->unique();
            $table->date('sales_delivery_date')->nullable();
            $table->integer('sales_invoice_id')->unsigned();
            $table->integer('sales_customer_id')->unsigned()->nullable();
            $table->string('sales_delivery_tender')->nullable();
            $table->integer('sales_warehouse_id')->unsigned();
            $table->string('sales_delivery_narration',255)->nullable();

			$table->string('sales_delivery_location',255);

            $table->decimal('sales_delivery_total_qty',18,2);
            $table->decimal('sales_delivery_sub_total_price',18,2);


			$table->decimal('sales_delivery_ati',18,2);
            $table->decimal('sales_delivery_commission',18,2);
            $table->decimal('sales_delivery_discount',18,2);
            $table->decimal('sales_delivery_vat',18,2);

            $table->string('sales_delivery_vat_type');

            $table->decimal('sales_delivery_total_price',18,2);
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
        Schema::dropIfExists('sales_delivery_challans');
    }
}
