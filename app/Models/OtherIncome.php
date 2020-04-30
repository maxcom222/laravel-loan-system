<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtherIncome extends Model
{
    protected $table = "other_income";

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function other_income_type()
    {
        return $this->hasOne(OtherIncomeType::class, 'id', 'other_income_type_id');
    }

    public function chart()
    {
        return $this->hasOne(ChartOfAccount::class, 'id', 'account_id');
    }
}
