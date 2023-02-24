<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLcVoucherTypeTable extends Migration
{
    public function up()
    {
        Schema::create('lc_voucher_type', function (Blueprint $table) {
            $table->increments('id');
            $table->string('voucher_type_name')->unique();
            $table->string('menu_link_db_tbl')->unique()->comment('Name of the respect Table');
            $table->string('transaction_column')->comment('Serial / Transaction Column Name');
            $table->text('narration')->nullable();
            $table->tinyInteger('status')->default('1');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lc_voucher_type');
    }
}
