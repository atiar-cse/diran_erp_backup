<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLcCommercialInvoiceEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lc_commercial_invoice_entries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lc_opening_info_id')->unsigned();
            $table->string('ci_no',100)->unique();
            $table->date('ci_date')->nullable();
            $table->string('ci_bill_of_entry_no',250)->nullable();
            $table->date('ci_bill_entry_date');
            $table->string('invoice_no')->nullable();
            $table->date('invoice_date')->nullable();
            $table->decimal('invoice_amount',13,2)->nullable();
            $table->date('eta_date')->nullable();
            $table->string('cnf_agent',255);
            $table->string('shipping_mode',150)->nullable();
            $table->date('receive_date')->nullable();
            $table->string('exp_no',155)->nullable();
            $table->string('vassel_name',150)->nullable();
            $table->string('shipping_line',150)->nullable();
            $table->string('depart_from',150)->nullable();
            $table->string('arrived_port',255)->nullable();
            $table->string('present_destination',255)->nullable();
            $table->integer('origin')->nullable();
            $table->text('narration')->nullable();
            $table->text('goods_name')->nullable();
            $table->integer('total_qty');
            $table->decimal('total_net_weight',13,2)->nullable();
            $table->decimal('total_gross_weight',13,2)->nullable();
            $table->decimal('total_amount',13,2);
            $table->tinyInteger('status')->default('1');
            $table->tinyInteger('approve_status')->default(0);
            $table->tinyInteger('ci_landed_status')->default(0);
            $table->tinyInteger('stock_enrty_status')->default(0);
            $table->tinyInteger('ci_delivery_type')->default(0);
            $table->decimal('ci_margin',13,2)->nullable();
            $table->decimal('ci_opening_charge',13,2)->nullable();
            $table->decimal('ci_bank_expenses',13,2)->nullable();
            $table->decimal('ci_insurance',13,2)->nullable();
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
        Schema::dropIfExists('lc_commercial_invoice_entries');
    }
}
