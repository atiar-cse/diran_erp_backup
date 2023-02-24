<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsFixedAssetDepreciationDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts_fixed_asset_depreciation_details', function (Blueprint $table) {
            $table->increments('id');
			
			$table->integer('depreciation_id')->unsigned();
			$table->integer('sub_code2_id')->unsigned();
			$table->integer('chart_acc_id')->unsigned();
		

            $table->decimal('amount',18,2);
            $table->integer('percentage');
			$table->decimal('depreciations',18,2);
            $table->decimal('current_amount',18,2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounts_fixed_asset_depreciation_details');
    }
}
