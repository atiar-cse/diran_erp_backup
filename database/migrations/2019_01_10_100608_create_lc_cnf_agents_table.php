<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLcCnfAgentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lc_cnf_agents', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cnf_agent_name',150);
            $table->string('cnf_agent_address',255)->nullable();
            $table->string('cnf_agent_contact_no',150)->nullable();
            $table->string('cnf_agent_email',150)->nullable();
            $table->string('narration',150)->nullable();
            $table->tinyInteger('status')->default('1');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();            
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
        Schema::dropIfExists('lc_cnf_agents');
    }
}
