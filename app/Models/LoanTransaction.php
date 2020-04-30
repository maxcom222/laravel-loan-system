<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanTransaction extends Model
{
    protected $table = "loan_transactions";

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function borrower()
    {
        return $this->hasOne(Borrower::class, 'id', 'borrower_id');
    }

    public function loan_repayment_method()
    {
        return $this->hasOne(LoanRepaymentMethod::class, 'id', 'repayment_method_id');
    }

    public function loan()
    {
        return $this->hasOne(Loan::class, 'id', 'loan_id');
    }
    public function journal_entries()
    {
        return $this->hasMany(JournalEntry::class, 'loan_transaction_id', 'id');
    }
}
