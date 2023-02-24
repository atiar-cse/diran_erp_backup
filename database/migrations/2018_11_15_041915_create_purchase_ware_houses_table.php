<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseWareHousesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_ware_houses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('purchase_ware_houses_name')->unique();
            $table->string('purchase_ware_houses_contact_person_name')->nullable();
            $table->string('purchase_ware_houses_mobile');
            $table->string('purchase_ware_houses_address',255);

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
        Schema::dropIfExists('purchase_ware_houses');
    }
}
