<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLcOthersChargeEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lc_others_charge_entries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('lc_others_charge_no');
            $table->integer('lc_other_charge_id')->unsigned();
            $table->integer('lc_opening_info_id')->unsigned();
            $table->integer('lc_commercial_invoice_id')->unsigned();
            $table->integer('cost_category_id')->unsigned();
            $table->date('others_charge_date')->nullable();

            $table->decimal('total_cost',18,2);

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
        Schema::dropIfExists('lc_others_charge_entries');
    }
}
