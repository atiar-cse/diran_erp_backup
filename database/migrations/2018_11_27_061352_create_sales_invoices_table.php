<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sales_invoices_no')->unique();
            $table->string('sales_invoices_contract_no')->nullable();
            $table->date('sales_invoices_date')->nullable();
            $table->enum('sales_invoices_type', ['Local', 'Tender']);
            $table->integer('sales_invoices_warehouse_id')->unsigned();
            $table->integer('sales_invoices_customer_id')->unsigned();
            $table->string('sales_invoices_narration',255)->nullable();
            $table->decimal('sales_invoices_total_qty',18,2);
            $table->decimal('sales_invoices_total_price',18,2);
			
			$table->tinyInteger('approve_status')->default('0');
			$table->tinyInteger('challan_add_status')->default('0');
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
        Schema::dropIfExists('sales_invoices');
    }
}
