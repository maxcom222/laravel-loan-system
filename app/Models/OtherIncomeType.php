<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtherIncomeType extends Model
{
    protected $table = "other_income_types";

    public $timestamps = false;

    public function chart()
    {
        return $this->hasOne(ChartOfAccount::class, 'id', 'account_id');
    }
}
