<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateJournalEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE journal_entries CHANGE COLUMN transaction_type transaction_type ENUM('repayment',
                    'disbursement',
                    'accrual',
                    'deposit',
                    'withdrawal',
                    'manual_entry',
                    'pay_charge',
                    'transfer_fund',
                    'expense',
                    'payroll',
                    'income',
                    'fee',
                    'penalty',
                    'interest',
                    'dividend',
                     'guarantee',
                    'close_write_off',
                    'repayment_disbursement',
                    'repayment_recovery',
                    'interest_accrual',
                    'fee_accrual') DEFAULT 'repayment'");
        Schema::table('journal_entries', function ($table) {
            $table->integer('loan_transaction_id')->nullable();
            $table->integer('branch_id')->nullable();
            $table->enum('transaction_sub_type',
                [
                    'overpayment',
                    'repayment_interest',
                    'repayment_principal',
                    'repayment_fees',
                    'repayment_penalty',
                ])->nullable();
            $table->tinyInteger('reversed')->default(0);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('journal_entries', function ($table) {
            $table->dropColumn('loan_transaction_id');
            $table->dropColumn('transaction_sub_type');
            $table->dropColumn('branch_id');
        });
    }
}
