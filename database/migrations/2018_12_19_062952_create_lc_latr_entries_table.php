<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLcLatrEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lc_latr_entries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lc_opening_info_id')->unsigned();
            $table->integer('lc_commercial_invoice_id')->unsigned();
            $table->date('latr_date')->nullable();
            $table->decimal('lc_margin_percentage',5,2)->nullable();
            $table->string('bank_latr_no')->nullable();
            $table->text('narration')->nullable();
            $table->integer('total_qty');
            $table->decimal('total_amount',13,2);
            $table->decimal('latr_percentage',5,2)->nullable();
			
			$table->decimal('bank_percentage',5,2)->nullable();
			$table->decimal('bank_percentage_amount',13,2)->nullable();
			
            $table->decimal('latr_total_amount',13,2);
            $table->tinyInteger('status')->default('1');
            $table->tinyInteger('approve_status')->default('0');
			
			$table->tinyInteger('account_status')->default('0');
			
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
        Schema::dropIfExists('lc_latr_entries');
    }
}
