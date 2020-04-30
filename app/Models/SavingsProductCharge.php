<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavingsProductCharge extends Model
{
    protected $table = "savings_product_charges";

    public function charge()
    {
        return $this->hasOne(Charge::class, 'id', 'charge_id');
    }
}
