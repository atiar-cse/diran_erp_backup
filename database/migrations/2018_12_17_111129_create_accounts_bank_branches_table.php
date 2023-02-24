<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsBankBranchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts_bank_branches', function (Blueprint $table) {
            $table->increments('id');
            $table->string('bank_branch_names')->unique();
            $table->string('bank_branch_codes')->unique();
            $table->integer('bank_id')->unsigned();
            $table->string('bank_branch_contact_no');
            $table->string('bank_branch_address',255)->nullable();
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
        Schema::dropIfExists('accounts_bank_branches');
    }
}
