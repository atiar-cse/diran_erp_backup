<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductionMassbodiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('production_massbodies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('massbody_no')->unique();
            $table->date('massbody_date');
            $table->integer('requisition_for_raw_material_id')->unsigned();;
            $table->text('narration')->nullable();
            $table->string('green_chip_name');
            
			$table->decimal('green_chip_percentage',18,2);
            $table->decimal('green_chip_weight',18,2);
            $table->decimal('green_chip_unit_price',13,2);
			
            $table->string('recycle_chip_name');
			
            $table->decimal('available_recycle_chip',13,2);
            $table->decimal('recycle_chip_percentage',18,2);
            $table->decimal('recycle_chip_weight',18,2);
            $table->decimal('recycle_chip_unit_price',13,2);
			
            $table->decimal('total_percentage',18,2);
            $table->decimal('total_weight_qty',18,2);
			
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
        Schema::dropIfExists('production_massbodies');
    }
}
