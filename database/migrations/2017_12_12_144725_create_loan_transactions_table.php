<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('branch_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('modified_by_id')->nullable();
            $table->integer('loan_id')->nullable();
            $table->integer('borrower_id')->nullable();
            $table->integer('loan_schedule_id')->nullable();
            $table->integer('repayment_method_id')->nullable();
            $table->enum('transaction_type',
                [
                    'repayment',
                    'repayment_disbursement',
                    'write_off',
                    'write_off_recovery',
                    'disbursement',
                    'interest_accrual',
                    'fee_accrual',
                    'penalty_accrual',
                    'deposit',
                    'withdrawal',
                    'manual_entry',
                    'pay_charge',
                    'transfer_fund',
                    'interest',
                    'income',
                    'fee',
                    'disbursement_fee',
                    'installment_fee',
                    'specified_due_date_fee',
                    'overdue_maturity',
                    'overdue_installment_fee',
                    'loan_rescheduling_fee',
                    'penalty',
                    'interest_waiver',
                    'charge_waiver'
                ])->default('repayment')->nullable();
            $table->text('name')->nullable();
            $table->decimal('debit', 65, 4)->nullable();
            $table->decimal('credit', 65, 4)->nullable();
            $table->decimal('balance', 65, 4)->nullable();
            $table->tinyInteger('reversible')->default(0);
            $table->tinyInteger('reversed')->default(0);
            $table->enum('reversal_type',
                [
                    'system',
                    'user',
                    'none'
                ])->default('none');
            $table->enum('payment_type',
                [
                    'interest',
                    'principal',
                    'regular'
                ])->default('regular')->nullable();
            $table->date('date')->nullable();
            $table->string('month')->nullable();
            $table->string('year')->nullable();
            $table->text('receipt')->nullable();
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
        Schema::drop('loan_transactions');
    }
}
