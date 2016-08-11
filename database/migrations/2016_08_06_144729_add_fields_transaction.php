<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsTransaction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->integer('property_id')->nullable();
            $table->integer('payment_from_id')->nullable();
            $table->integer('payment_from_source')->nullable();
            $table->integer('payment_from_unit')->nullable();
            $table->string('status', 20)->nullable();
            $table->string('payment_type', 20)->nullable();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function(Blueprint $table) {
            $table->dropColumn('property_id');
            $table->dropColumn('payment_from_id');
            $table->dropColumn('payment_from_source');
            $table->dropColumn('payment_from_unit');
            $table->dropColumn('payment_type');
            $table->dropColumn('status');
        });
    }
}
