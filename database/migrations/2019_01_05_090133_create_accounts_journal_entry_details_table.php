<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsJournalEntryDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts_journal_entry_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('journal_entry_id')->unsigned();
            $table->integer('char_of_account_id')->unsigned();
			 $table->integer('type_id')->unsigned()->nullable();
            $table->string('type_desc')->nullable();
            $table->string('remarks')->nullable();

            $table->decimal('debit_amount',18,2)->default(0);
            $table->decimal('credit_amount',18,2)->default(0);

            $table->integer('voucher_type_id')->nullable();
            $table->integer('voucher_ref_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounts_journal_entry_details');
    }
}
