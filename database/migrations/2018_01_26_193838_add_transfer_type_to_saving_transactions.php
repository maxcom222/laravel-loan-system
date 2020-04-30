<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTransferTypeToSavingTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE savings_transactions CHANGE COLUMN type type ENUM( 'deposit',
                'withdrawal',
                'bank_fees',
                'interest',
                'dividend',
                'guarantee',
                'guarantee_restored',
                'fees_payment',
                'transfer_loan',
                'transfer_savings')");
        Schema::table('savings_transactions', function ($table) {
            $table->integer('reference')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('savings_transactions', function ($table) {
            $table->dropColumn('reference');

        });
    }
}
