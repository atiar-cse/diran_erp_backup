<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLcInsurancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lc_insurances', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lc_opening_info_id')->unsigned();
            $table->string('insurance_company')->nullable();
            $table->date('insurance_date')->nullable();
            $table->string('insurance_no',100)->nullable();
            $table->decimal('insurance_amount',18,2)->nullable();
            $table->text('narration')->nullable();
            $table->tinyInteger('approve_status')->default('0');
			$table->tinyInteger('account_status')->default('0');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('approve_by')->nullable();
            $table->dateTime('approve_at')->nullable();
            $table->integer('acc_approve_by')->nullable();
            $table->integer('acc_updated_by')->nullable();
            $table->dateTime('acc_approve_at')->nullable();
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
        Schema::dropIfExists('lc_insurances');
    }
}
