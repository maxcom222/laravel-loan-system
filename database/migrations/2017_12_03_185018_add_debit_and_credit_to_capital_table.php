<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDebitAndCreditToCapitalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('capital', function ($table) {
            $table->integer('credit_account_id')->nullable();
            $table->integer('debit_account_id')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('capital', function ($table) {
            $table->dropColumn('credit_account_id');
            $table->dropColumn('debit_account_id');
        });
    }
}
