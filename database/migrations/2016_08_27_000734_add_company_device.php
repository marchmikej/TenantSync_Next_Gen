<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCompanyDevice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->integer('company_id')->nullable();
        });
        Schema::table('auto_payments', function (Blueprint $table) {
            $table->integer('company_id')->nullable();
        });
        Schema::create('companies', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('name', 30);
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
        Schema::table('devices', function(Blueprint $table) {
            $table->dropColumn('company_id');
        });
        Schema::table('auto_payments', function(Blueprint $table) {
            $table->dropColumn('company_id');
        });
        Schema::drop('companies');        
    }
}
