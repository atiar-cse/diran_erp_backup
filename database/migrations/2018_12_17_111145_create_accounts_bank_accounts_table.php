<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsBankAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts_bank_accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('accounts_bank_id')->unsigned();
            $table->integer('accounts_branch_id')->unsigned();

            $table->string('accounts_bank_accounts_name');
            $table->string('accounts_bank_accounts_number')->unique();

            $table->date('accounts_bank_accounts_date')->nullable();

			$table->integer('chart_ac_id')->unsigned();

            $table->string('accounts_bank_accounts_contact_person')->nullable();
            $table->string('accounts_bank_accounts_contact_number')->nullable();


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
        Schema::dropIfExists('accounts_bank_accounts');
    }
}
