<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAccountingToLoanProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loan_products', function ($table) {
            $table->enum('accounting_rule', [
                'cash_based',
                'accrual_periodic',
                'accrual_upfront'
            ])->default('cash_based')->nullable();
            $table->integer('chart_fund_source_id')->nullable();
            $table->integer('chart_loan_portfolio_id')->nullable();
            $table->integer('chart_receivable_interest_id')->nullable();
            $table->integer('chart_receivable_fee_id')->nullable();
            $table->integer('chart_receivable_penalty_id')->nullable();
            $table->integer('chart_loan_over_payments_id')->nullable();
            $table->integer('chart_income_interest_id')->nullable();
            $table->integer('chart_income_fee_id')->nullable();
            $table->integer('chart_income_penalty_id')->nullable();
            $table->integer('chart_income_recovery_id')->nullable();
            $table->integer('chart_loans_written_off_id')->nullable();
            $table->enum('after_maturity_date_penalty_system_type', [
                'system',
                'user',
            ])->default('system')->nullable();
            $table->text('after_maturity_date_penalties')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('loan_products', function ($table) {
            $table->dropColumn([
                'accounting_rule',
                'chart_fund_source_id',
                'chart_loan_portfolio_id',
                'chart_loan_over_payments_id',
                'chart_income_interest_id',
                'chart_income_fee_id',
                'chart_income_penalty_id',
                'chart_income_recovery_id',
                'chart_loans_written_off_id',
                'after_maturity_date_penalty_system_type',
                'after_maturity_date_penalties',
                'chart_receivable_interest_id',
                'chart_receivable_fee_id',
                'chart_receivable_penalty_id'
            ]);
        });
    }
}
