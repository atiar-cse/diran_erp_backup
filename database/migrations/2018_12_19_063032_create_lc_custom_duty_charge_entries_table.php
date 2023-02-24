<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLcCustomDutyChargeEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lc_custom_duty_charge_entries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lc_opening_info_id')->unsigned();
            $table->integer('lc_commercial_invoice_id')->unsigned();
            $table->date('custom_duty_date')->nullable();
            $table->integer('custom_duty_cost_id')->unsigned();

            $table->decimal('total_cost',18,2);

            $table->integer('cnf_agent_id')->unsigned();
            $table->text('narration')->nullable();
            $table->tinyInteger('status')->default('1');
            $table->tinyInteger('approve_status')->default('0');
			$table->tinyInteger('account_status')->default('0');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('approve_by')->nullable();
            $table->dateTime('approve_at')->nullable();
            $table->integer('acc_approve_by')->nullable();
            $table->integer('acc_updated_by')->nullable();
            $table->dateTime('acc_approve_at')->nullable();
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
        Schema::dropIfExists('lc_custom_duty_charge_entries');
    }
}
