<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Capital extends Model
{
    protected $table = "capital";

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    public function bank()
    {
        return $this->hasOne(BankAccount::class, 'id', 'bank_account_id');
    }
    public function debit_chart()
    {
        return $this->hasOne(ChartOfAccount::class, 'id', 'debit_account_id');
    }
    public function credit_chart()
    {
        return $this->hasOne(ChartOfAccount::class, 'id', 'credit_account_id');
    }
}
