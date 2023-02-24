<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLcStockEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lc_stock_entries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lc_opening_info_id')->unsigned();
            $table->integer('lc_commercial_invoice_id')->unsigned();
            $table->integer('warehouse_id')->unsigned();
            $table->integer('supplier_id')->unsigned();
            $table->date('entry_date');
            $table->text('narration')->nullable();
            $table->integer('total_qty');
            $table->decimal('total_net_weight',18,2)->nullable();
            $table->decimal('total_gross_weight',18,2)->nullable();
            $table->decimal('total_amount',18,2);
            $table->tinyInteger('status')->default('1');
            $table->tinyInteger('approve_status')->default(0);
			$table->tinyInteger('account_status')->default(0);
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
        Schema::dropIfExists('lc_stock_entries');
    }
}
