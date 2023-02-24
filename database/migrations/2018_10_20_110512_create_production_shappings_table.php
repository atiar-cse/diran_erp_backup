<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductionShappingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('production_shappings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('shapping_no')->unique();
            $table->date('shapping_date');
            $table->text('narration')->nullable();
            $table->decimal('total_trans_to_dryer_qty',18,2);
            $table->decimal('total_trans_to_dryer_weight',18,2);
			$table->decimal('total_receive_qty',18,2);
			$table->decimal('total_remain_qty',18,2);
            $table->decimal('total_waste_qty',18,2);
            $table->decimal('total_waste_weight',18,2);
            $table->decimal('process_loss_percentage',18,2)->nullable();
            $table->decimal('process_loss_weight',18,2)->nullable();
            $table->decimal('weight_after_process_loss',18,2);
            $table->decimal('total_amount',13,2);
            $table->text('overhead_info')->nullable();
            $table->decimal('wastage_overhead_amt',13,2)->default(0);
            
            $table->tinyInteger('approve_status')->default('0');
            $table->tinyInteger('account_status')->default('0');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('approve_by')->nullable();
            $table->integer('acc_approve_by')->nullable();
            $table->dateTime('approve_at')->nullable();
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
        Schema::dropIfExists('production_shappings');
    }
}
