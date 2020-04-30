<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavingsCharge extends Model
{
    protected $table = "savings_charges";

    public function charge()
    {
        return $this->hasOne(Charge::class, 'id', 'charge_id');
    }
}
