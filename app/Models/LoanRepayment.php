<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoanRepayment extends Model
{
    use SoftDeletes;
    protected $table = "loan_repayments";

    public function schedules()
    {
        return $this->hasMany(LoanSchedule::class, 'loan_id', 'id');
    }

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

    public function loan_disbursed_by()
    {
        return $this->hasOne(LoanDisbursedBy::class, 'id', 'loan_disbursed_by_id');
    }

    public function loan_repayment_method()
    {
        return $this->hasOne(LoanRepaymentMethod::class, 'id', 'repayment_method_id');
    }
}
