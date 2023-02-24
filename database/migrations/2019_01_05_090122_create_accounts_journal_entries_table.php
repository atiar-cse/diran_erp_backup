<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsJournalEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts_journal_entries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('transaction_reference_id')->nullable();
            $table->string('transaction_reference_no')->nullable();
            $table->date('transaction_date');
            
            $table->enum('type', ['customer', 'supplier', 'employee', 'lc'])->nullable();
            
            $table->smallInteger('transaction_type');
            $table->tinyInteger('is_flash')->default('0')->comment('Opening Balance to Match Provident Fund, Emp Advance etc');
            $table->string('transaction_type_name')->nullable();
            $table->integer('party_id')->unsigned();
            $table->integer('cost_center_id')->unsigned()->nullable();
            $table->integer('branch_id')->unsigned()->nullable();
            $table->integer('bank_id')->unsigned()->nullable();;
            $table->string('bank_reference')->nullable();;
            $table->decimal('total_debit',18,2);
            $table->decimal('total_credit',18,2);
            $table->text('narration')->nullable();
            $table->tinyInteger('approve_status')->default('0');
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
        Schema::dropIfExists('accounts_journal_entries');
    }
}
