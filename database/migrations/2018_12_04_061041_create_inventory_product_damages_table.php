<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventoryProductDamagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_product_damages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('inventory_product_damages_no')->unique();
            $table->date('inventory_product_damages_date')->nullable();
            $table->integer('inventory_product_damages_warehouse_id')->unsigned();
            $table->string('inventory_product_damages_narration',255)->nullable();
            /*$table->decimal('inventory_product_damages_total_qty',18,2);
            $table->decimal('inventory_product_damages_total_price',18,2);*/
            
			$table->tinyInteger('approve_status')->default('0');
            $table->tinyInteger('status')->default('1');
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
        Schema::dropIfExists('inventory_product_damages');
    }
}
