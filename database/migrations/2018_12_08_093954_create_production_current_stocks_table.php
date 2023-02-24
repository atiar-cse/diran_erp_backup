<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductionCurrentStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('production_current_stocks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id')->unsigned();

            $table->decimal('forming_current_qty',13,2)->default(0);
			$table->decimal('forming_rececive_qty',13,2)->default(0);
			
            $table->decimal('shapping_current_qty',13,2)->default(0);
			$table->decimal('shapping_rececive_qty',13,2)->default(0);
			
            $table->decimal('dryer_current_qty',13,2)->default(0);
			$table->decimal('dryer_receive_qty',13,2)->default(0);
			
            $table->decimal('glaze_current_qty',13,2)->default(0);
			$table->decimal('glaze_receive_qty',13,2)->default(0);
			
            $table->decimal('kiln_current_qty',13,2)->default(0);
			$table->decimal('kiln_receive_qty',13,2)->default(0);
            
            $table->decimal('testing_current_qty',13,2)->default(0);
            $table->decimal('testing_receive_qty',13,2)->default(0);

/*			
            $table->decimal('semi_finished_stk_current_qty',13,2)->default(0)->comment('Semi Finished Stock Opening Qty');
			$table->decimal('semi_finished_stk_receive_qty',13,2)->default(0);
*/

            $table->decimal('finished_stk_current_qty',13,2)->default(0);
            $table->decimal('finished_stk_receive_qty',13,2)->default(0);

            $table->decimal('assembling_current_qty',13,2)->default(0);
			$table->decimal('assembling_receive_qty',13,2)->default(0);
			
            $table->decimal('packing_current_qty',13,2)->default(0);
			$table->decimal('packing_receive_qty',13,2)->default(0);
			
        });

        //DB::statement("ALTER TABLE `production_current_stocks` comment 'semi_finished_stk_current_qty, finished_stk_receive_qty are not usning'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('production_current_stocks');
    }
}
