<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SavingTransaction extends Model
{
    protected $table = "savings_transactions";



    public function borrower()
    {
        return $this->hasOne(Borrower::class, 'id', 'borrower_id');
    }
    public function payment_method()
    {
        return $this->hasOne(LoanRepaymentMethod::class, 'id', 'payment_method_id');
    }
    public function savings()
    {
        return $this->hasOne(Saving::class, 'id', 'savings_id');
    }
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

}
