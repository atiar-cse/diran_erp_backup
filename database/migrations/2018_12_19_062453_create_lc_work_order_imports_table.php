<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLcWorkOrderImportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lc_work_order_imports', function (Blueprint $table) {
            $table->increments('id');
            $table->string('work_order_no')->unique();
            $table->integer('supplier_id')->unsigned();
            $table->date('order_date');
            $table->text('narration')->nullable();
            $table->integer('total_qty');
            $table->decimal('total_amount',13,2);
            $table->decimal('total_amount_taka',13,2);
            $table->tinyInteger('status')->default('1');
            $table->tinyInteger('approve_status')->default(0);
            $table->tinyInteger('closing_status')->default(0);
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
        Schema::dropIfExists('lc_work_order_imports');
    }
}
