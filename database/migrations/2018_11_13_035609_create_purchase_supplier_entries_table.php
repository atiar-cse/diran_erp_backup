<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseSupplierEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_supplier_entries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('purchase_supplier_name')->unique();
            $table->string('purchase_supplier_id');
            $table->integer('chart_ac_id')->unsigned();
            $table->date('purchase_supplier_join_date')->nullable();

            $table->string('purchase_supplier_contact_person_name');
            $table->string('purchase_supplier_contact_person_job_title')->nullable();
            $table->string('purchase_supplier_business_phone');

            $table->string('purchase_supplier_mobile');
            $table->string('purchase_supplier_email')->nullable();
            $table->string('purchase_supplier_web_address')->nullable();
            $table->decimal('purchase_supplier_credit_limit', 18,2)->nullable();

            $table->string('purchase_supplier_office_address',255)->nullable();
            $table->string('purchase_supplier_narration',255)->nullable();

            $table->tinyInteger('is_importer')->default('0');
            $table->tinyInteger('status')->default('1');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('purchase_supplier_entries');
    }
}
