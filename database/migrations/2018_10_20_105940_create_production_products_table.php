<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductionProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('production_products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('product_name')->unique();
            $table->string('product_code')->nullable();
            $table->integer('category_id')->unsigned()->nullable();
            $table->integer('brand_id')->unsigned()->nullable();
            $table->integer('measure_unit_id')->unsigned();
            $table->integer('country_id')->unsigned()->nullable();
            $table->integer('credit_limit')->nullable();

            $table->decimal('buying_price',18,2)->nullable();
            $table->decimal('selling_price',18,2)->nullable();
            $table->decimal('complete_product_weight',18,2)->nullable();
            $table->decimal('forming_weight',18,2)->nullable();
            $table->decimal('shapping_weight',18,2)->nullable();
            $table->decimal('dryer_weight',18,2)->nullable();
            $table->decimal('glaze_weight',18,2)->nullable();
            $table->decimal('kiln_weight',18,2)->nullable();

            $table->text('product_description')->nullable();
            $table->tinyInteger('is_raw_material_status')->default('0')->nullable();
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
        Schema::dropIfExists('production_products');
    }
}
