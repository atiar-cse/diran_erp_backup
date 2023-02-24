<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsSalesInvoiceVoucherDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts_sales_invoice_voucher_details', function (Blueprint $table) {
           $table->increments('id');
           $table->integer('ac_sales_voucher_id')->unsigned();
           $table->integer('product_id')->unsigned();
           $table->string('remarks')->nullable();
           $table->string('m_unit');
           $table->decimal('unit_price',18,2);
           $table->string('discount_type');
           $table->decimal('discount',18,2);
           $table->string('vat_sub');
           $table->decimal('qty',18,2);
           $table->decimal('total_price',18,2);
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
        Schema::dropIfExists('accounts_sales_invoice_voucher_details');
    }
}
