<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanCharge extends Model
{
    protected $table = "loan_charges";

    public function charge()
    {
        return $this->hasOne(Charge::class, 'id', 'charge_id');
    }
}
