<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJournalEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable();
            $table->integer('account_id')->nullable();
            $table->enum('transaction_type',
                [
                    'repayment',
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
                    'penalty'
                ])->default('repayment')->nullable();
            $table->text('name')->nullable();
            $table->string('gl_code')->nullable();
            $table->date('date')->nullable();
            $table->string('month')->nullable();
            $table->string('year')->nullable();
            $table->string('reference')->nullable();
            $table->integer('borrower_id')->nullable();
            $table->integer('loan_id')->nullable();
            $table->integer('expense_id')->nullable();
            $table->integer('capital_id')->nullable();
            $table->integer('income_id')->nullable();
            $table->integer('payroll_id')->nullable();
            $table->integer('savings_id')->nullable();
            $table->integer('loan_repayment_id')->nullable();
            $table->decimal('debit', 65, 4)->nullable();
            $table->decimal('credit', 65, 4)->nullable();
            $table->decimal('balance', 65, 4)->nullable();
            $table->tinyInteger('active')->default(1);
            $table->text('notes')->nullable();
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
        Schema::drop('journal_entries');
    }
}
