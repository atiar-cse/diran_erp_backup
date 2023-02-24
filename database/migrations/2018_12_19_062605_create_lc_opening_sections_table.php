<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLcOpeningSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lc_opening_sections', function (Blueprint $table) {
            $table->increments('id');
            $table->string('lc_no',150)->unique();
            $table->integer('supplier_id')->unsigned();
            $table->integer('proforma_invoice_id')->unsigned()->unique();
            $table->tinyInteger('lc_category')->nullable();
            $table->tinyInteger('lc_type')->nullable();
            $table->date('lc_opening_date');
            $table->decimal('lc_opening_charges',18,2)->nullable();
            $table->integer('lc_opening_bank_id')->unsigned();
            $table->decimal('lc_bank_expenses',18,2)->nullable();
            $table->string('baneficiary_bank',255)->nullable();
            $table->date('lc_latest_shipment_date')->nullable();
            $table->date('lc_expire_date')->nullable();
            $table->decimal('lc_total_value',18,2);
            $table->decimal('insurance_amount',18,2)->default('0');
            
            $table->tinyInteger('lc_status')->default('1');
            $table->tinyInteger('status')->default('1');
            $table->tinyInteger('approve_status')->default('0');
			$table->tinyInteger('account_status')->default('0');
            $table->tinyInteger('lc_closing_status')->default('0');
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
        Schema::dropIfExists('lc_opening_sections');
    }
}
