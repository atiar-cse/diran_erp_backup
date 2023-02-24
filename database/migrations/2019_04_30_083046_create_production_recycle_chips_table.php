<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductionRecycleChipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('production_recycle_chips', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('recycle_chip_current_weight',13,2)
                            ->comment('RecycleChip Current/Open Weight');

            $table->decimal('recycle_chip_unit_price',9,2);
            $table->decimal('recycle_chip_total_amt',13,2);

            $table->decimal('glaze_material_price_per_kg',9,2);

            $table->decimal('last_month_overhead_amt',13,2);
            $table->decimal('last_month_production_kg',13,2);
            $table->decimal('overhead_per_kg',13,2);

            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();            
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
        Schema::dropIfExists('production_recycle_chips');
    }
}
