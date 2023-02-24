<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsChartofAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts_chartof_accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('main_code_id')->unsigned()->nullable();
            $table->integer('sub_code_id')->unsigned()->nullable();
            $table->integer('sub_code2_id')->unsigned();
            $table->integer('branch_id')->unsigned()->nullable();
            $table->string('chart_of_accounts_title')->unique();
            $table->integer('chart_of_account_code')->unique()->nullable();
            $table->string('accounts_status')->nullable();
            $table->decimal('current_balance',18,2)->default(0)->nullable();
            $table->date('opening_date')->nullable();
            $table->decimal('opening_balance',18,2)->default(0)->nullable();
            $table->date('closing_date')->nullable();
            $table->decimal('closing_balance',18,2)->nullable();
            $table->text('narration')->nullable();
			
			$table->tinyInteger('open_bl_add_status')->default('0');
			
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
        Schema::dropIfExists('accounts_chartof_accounts');
    }
}
