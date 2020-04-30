<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Borrower extends Model
{
    use SoftDeletes;
    protected $table = "borrowers";

    public function loans()
    {
        return $this->hasMany(Loan::class, 'borrower_id', 'id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function payments()
    {
        return $this->hasMany(LoanRepayment::class, 'borrower_id', 'id');
    }

    public function country()
    {
        return $this->hasOne(Country::class, 'id', 'country_id');
    }
}
