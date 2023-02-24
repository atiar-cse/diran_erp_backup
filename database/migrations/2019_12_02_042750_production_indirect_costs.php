<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProductionIndirectCosts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('production_indirect_costs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('indir_no',150)->unique();
            $table->date('date');
            
            $table->decimal('total_amount',13,2);
            $table->string('remarks')->nullable();

            $table->tinyInteger('approve_status')->default(0);
            $table->tinyInteger('account_status')->default(0);
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
        Schema::dropIfExists('production_indirect_costs');
    }
}
