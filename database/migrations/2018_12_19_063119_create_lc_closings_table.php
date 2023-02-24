<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLcClosingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lc_closings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lc_opening_info_id')->unsigned();
            $table->integer('supplier_id')->unsigned();
            $table->date('closing_date');
            $table->text('narration')->nullable();
            $table->decimal('total_cost',13,2)->nullable();
            $table->decimal('landed_cost',13,2)->nullable();
            $table->tinyInteger('status')->default('1');
            $table->tinyInteger('approve_status')->default(0);
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
        Schema::dropIfExists('lc_closings');
    }
}
