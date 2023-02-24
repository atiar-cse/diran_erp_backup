<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsMonthlyClosingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts_monthly_closings', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date')->nullable();
            $table->integer('chart_of_account_id')->unsigned()->nullable();
            $table->integer('chart_of_account_code')->unsigned()->nullable();
            $table->decimal('opening_balance',18,2)->default(0)->nullable();
            $table->decimal('current_balance',18,2)->default(0)->nullable();
            $table->decimal('closing_balance',18,2)->nullable();
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
        Schema::dropIfExists('accounts_monthly_closings');
    }
}
