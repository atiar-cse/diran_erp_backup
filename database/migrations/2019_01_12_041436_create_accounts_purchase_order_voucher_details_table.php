<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsPurchaseOrderVoucherDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts_purchase_order_voucher_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ac_pruchase_stock_voucher_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->string('remarks')->nullable();
            $table->string('m_unit')->nullable();

            $table->decimal('unit_price',18,2)->nullable();
            $table->decimal('qty',18,2)->nullable();
            $table->decimal('total_price',18,2)->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounts_purchase_order_voucher_details');
    }
}
