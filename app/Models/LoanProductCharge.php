<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanProductCharge extends Model
{
    protected $table = "loan_product_charges";
    public function charge()
    {
        return $this->hasOne(Charge::class, 'id', 'charge_id');
    }
}
