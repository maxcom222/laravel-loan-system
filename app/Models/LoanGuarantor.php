<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanGuarantor extends Model
{
    protected $table = "loan_guarantors";

    public $timestamps = false;

    public function borrower()
    {
        return $this->hasOne(Borrower::class, 'id', 'borrower_id');
    }
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    public function loan()
    {
        return $this->hasOne(Loan::class, 'id', 'loan_id');
    }
    public function guarantor()
    {
        return $this->hasOne(Guarantor::class, 'id', 'guarantor_id');
    }
}
