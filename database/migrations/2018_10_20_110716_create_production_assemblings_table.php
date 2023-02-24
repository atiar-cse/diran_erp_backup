<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductionAssemblingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('production_assemblings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('assembling_no')->unique();
            $table->date('assembling_date');
            $table->integer('warehouse_id')->unsigned();
            $table->string('assembling_types');
            $table->integer('finishing_product_id')->unsigned()->nullable();

            $table->decimal('trans_to_packing_qty',18,2)->nullable();
            $table->decimal('unit_price',18,2)->nullable();
            $table->decimal('total_price',18,2)->nullable();
            $table->text('overhead_info')->nullable();

            $table->text('narration')->nullable();

            $table->decimal('total_rm_qty',18,2);
            $table->decimal('total_rm_price',18,2);

            $table->decimal('inv_total_qty',13,2);
            $table->decimal('inv_total_amount',13,2);

            $table->tinyInteger('approve_status')->default('0');
            $table->tinyInteger('account_status')->default('0');

            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('approve_by')->nullable();
            $table->dateTime('approve_at')->nullable();

            $table->integer('acc_approve_by')->nullable();
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
        Schema::dropIfExists('production_assemblings');
    }
}
