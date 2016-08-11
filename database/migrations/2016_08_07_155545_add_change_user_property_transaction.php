<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddChangeUserPropertyTransaction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_property', function(Blueprint $table)
        {
            $table->integer('device_id');   
            $table->dropColumn('unit');     
            $table->dropColumn('property_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_property', function(Blueprint $table) {
            $table->dropColumn('device_id');
            $table->string('unit');
            $table->integer('property_id');
        });
    }
}
