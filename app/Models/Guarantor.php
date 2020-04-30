<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guarantor extends Model
{
    protected $table = "guarantor";

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function borrower()
    {
        return $this->hasOne(Borrower::class, 'id', 'borrower_id');
    }

    public function country()
    {
        return $this->hasOne(Country::class, 'id', 'country_id');
    }

    public function loans()
    {
        return $this->hasMany(LoanGuarantor::class, 'guarantor_id', 'id');
    }
}
