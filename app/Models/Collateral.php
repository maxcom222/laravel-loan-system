<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Collateral extends Model
{
    protected $table = "collateral";

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function collateral_type()
    {
        return $this->hasOne(CollateralType::class, 'id', 'collateral_type_id');
    }

    public function loan()
    {
        return $this->hasOne(Loan::class, 'id', 'loan_id');
    }

    public function borrower()
    {
        return $this->hasOne(Borrower::class, 'id', 'borrower_id');
    }
}
