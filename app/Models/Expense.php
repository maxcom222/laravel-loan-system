<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $table = "expenses";

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    public function expense_type()
    {
        return $this->hasOne(ExpenseType::class, 'id', 'expense_type_id');
    }
    public function chart()
    {
        return $this->hasOne(ChartOfAccount::class, 'id', 'account_id');
    }
}
