<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    protected $table = "payroll";

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    public function chart()
    {
        return $this->hasOne(ChartOfAccount::class, 'id', 'chart_id');
    }
}
