<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseType extends Model
{
    protected $table = "expense_types";

    public $timestamps = false;

    public function chart()
    {
        return $this->hasOne(ChartOfAccount::class, 'id', 'account_id');
    }
}
