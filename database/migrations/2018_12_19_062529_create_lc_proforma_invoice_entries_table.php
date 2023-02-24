<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLcProformaInvoiceEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lc_proforma_invoice_entries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('pi_no',150)->unique();
            $table->integer('work_order_id')->unsigned();
            $table->integer('supplier_id')->unsigned();
            $table->string('usd_account_no',100)->nullable();
            $table->date('pi_date')->nullable();
            $table->string('consignee',250)->nullable();
            $table->string('swift_code',150)->nullable();
            $table->string('terms_of_loading',255);
            $table->string('port_of_discharger',255);
            $table->date('delivery_time')->nullable();
            $table->string('terms_of_delivery',150)->nullable();
            $table->string('terms_of_payment',150)->nullable();
            $table->integer('origin_of_goods')->unsigned()->nullable();
            $table->string('final_destination',150)->nullable();
            $table->text('narration')->nullable();
            $table->integer('total_qty');
            $table->decimal('total_amount',13,2);
            $table->decimal('total_amount_taka',13,2);


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
        Schema::dropIfExists('lc_proforma_invoice_entries');
    }
}
