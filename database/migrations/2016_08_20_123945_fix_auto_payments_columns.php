<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixAutoPaymentsColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('auto_payments', function(Blueprint $table) {
            $table->float('transaction_fee', 30)->default(0.00)->change();
            $table->text('description', 65535)->nullable();
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
            $table->dropColumn('description');
        });
    }
}
