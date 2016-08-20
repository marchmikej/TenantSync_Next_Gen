<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAutoPayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auto_payments', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('device_id')->nullable();
            $table->integer('customer_number');
            $table->string('schedule', 30);
            $table->date('initial_date')->nullable();
            $table->integer('num_payments');
            $table->float('amount', 30)->default(0.00);
            $table->integer('transaction_fee');
            $table->string('payment_type',10)->nullable();
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
        Schema::drop('auto_payments');
    }

}
