<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsFixedAssetDepreciationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts_fixed_asset_depreciations', function (Blueprint $table) {
            $table->increments('id');
			$table->string('depreciations_no')->unique();
            $table->date('depreciations_date')->nullable();
           
            $table->integer('sub_code_id')->unsigned();
            $table->string('depreciations_narration',255)->nullable();

            $table->decimal('total_amount',18,2);


			$table->decimal('total_depreciations',18,2);
            $table->decimal('total_current_amount',18,2);
			
			$table->tinyInteger('approve_status')->default('0');
			$table->tinyInteger('account_status')->default('0');
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
        Schema::dropIfExists('accounts_fixed_asset_depreciations');
    }
}
