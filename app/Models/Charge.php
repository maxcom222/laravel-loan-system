<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Charge extends Model
{
    protected $table = "charges";

    public function savings_charges()
    {
        return $this->hasMany(SavingsCharge::class, 'charge_id', 'id');
    }
}
