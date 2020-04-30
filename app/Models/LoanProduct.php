<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanProduct extends Model
{
    protected $table = "loan_products";
    public $timestamps = false;

    public function charges()
    {
        return $this->hasMany(LoanProductCharge::class, 'loan_product_id', 'id');
    }
    public function chart_loan_portfolio()
    {
        return $this->hasOne(ChartOfAccount::class, 'id', 'chart_loan_portfolio_id');
    }
    public function chart_fund_source()
    {
        return $this->hasOne(ChartOfAccount::class, 'id', 'chart_fund_source_id');
    }
    public function chart_receivable_interest()
    {
        return $this->hasOne(ChartOfAccount::class, 'id', 'chart_receivable_interest_id');
    }
    public function chart_receivable_fee()
    {
        return $this->hasOne(ChartOfAccount::class, 'id', 'chart_receivable_fee_id');
    }
    public function chart_receivable_penalty()
    {
        return $this->hasOne(ChartOfAccount::class, 'id', 'chart_receivable_penalty_id');
    }
    public function chart_loan_overpayment()
    {
        return $this->hasOne(ChartOfAccount::class, 'id', 'chart_loan_over_payments_id');
    }
    public function chart_income_interest()
    {
        return $this->hasOne(ChartOfAccount::class, 'id', 'chart_income_interest_id');
    }
    public function chart_income_fee()
    {
        return $this->hasOne(ChartOfAccount::class, 'id', 'chart_income_fee_id');
    }
    public function chart_income_penalty()
    {
        return $this->hasOne(ChartOfAccount::class, 'id', 'chart_income_penalty_id');
    }
    public function chart_income_recovery()
    {
        return $this->hasOne(ChartOfAccount::class, 'id', 'chart_income_recovery_id');
    }
    public function chart_loans_written_off()
    {
        return $this->hasOne(ChartOfAccount::class, 'id', 'chart_loans_written_off_id');
    }
}
