<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SavingProduct extends Model
{
    protected $table = "savings_products";


    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function charges()
    {
        return $this->hasMany(SavingsProductCharge::class, 'savings_product_id', 'id');
    }

    public function chart_reference()
    {
        return $this->hasOne(ChartOfAccount::class, 'id', 'chart_reference_id');
    }

    public function chart_overdraft_portfolio()
    {
        return $this->hasOne(ChartOfAccount::class, 'id', 'chart_overdraft_portfolio_id');
    }

    public function chart_control()
    {
        return $this->hasOne(ChartOfAccount::class, 'id', 'chart_savings_control_id');
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

    public function chart_expense_interest()
    {
        return $this->hasOne(ChartOfAccount::class, 'id', 'chart_expense_interest_id');
    }

    public function chart_expense_written_off()
    {
        return $this->hasOne(ChartOfAccount::class, 'id', 'chart_expense_written_off_id');
    }

}
