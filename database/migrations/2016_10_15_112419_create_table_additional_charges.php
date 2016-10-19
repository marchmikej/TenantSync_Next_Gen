<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableAdditionalCharges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('additional_charges', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('payment_type',80);
            $table->integer('device_id');
            $table->integer('amount');
            $table->timestamps();
        });
        Schema::table('devices', function(Blueprint $table) {
            $table->dropColumn('additional_charges');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('additional_charges');
    }
}
