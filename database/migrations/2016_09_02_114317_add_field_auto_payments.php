<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldAutoPayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('auto_payments', function (Blueprint $table) {
            $table->string('status', 10)->default('active');
            $table->integer('payments_processed')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('auto_payments', function(Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('payments_processed');
        });
    }
}
