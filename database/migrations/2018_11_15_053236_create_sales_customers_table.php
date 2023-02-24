<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_customers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sales_customer_name')->unique();
            $table->string('sales_customer_id');
            $table->integer('chart_ac_id')->unsigned();
            $table->date('sales_customer_join_date')->nullable();

            $table->string('sales_customer_contact_person_name');
            $table->string('sales_customer_contact_person_job_title')->nullable();
            $table->string('sales_customer_business_phone');

            $table->string('sales_customer_mobile');
            $table->string('sales_customer_email')->nullable();
            $table->string('sales_customer_web_address')->nullable();
            $table->decimal('sales_customer_credit_limit', 18,2)->nullable();

            $table->string('sales_customer_office_address',255)->nullable();
            $table->string('sales_customer_narration',255)->nullable();

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
        Schema::dropIfExists('sales_customers');
    }
}
