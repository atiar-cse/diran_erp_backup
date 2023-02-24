<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLcCfValueMarginEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lc_cf_value_margin_entries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lc_opening_info_id')->unsigned()->unique();
            $table->integer('supplier_id')->unsigned();
            $table->date('margin_entry_date')->nullable();
            $table->integer('bank_id')->unsigned();
            $table->decimal('lc_value',13,2);
            $table->decimal('margin_percentage',5,2)->nullable();
            $table->decimal('margin_value',13,2);
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
        Schema::dropIfExists('lc_cf_value_margin_entries');
    }
}
