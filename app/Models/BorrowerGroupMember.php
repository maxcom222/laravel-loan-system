<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BorrowerGroupMember extends Model
{
    protected $table = "borrower_group_members";


    public function group()
    {
        return $this->hasOne(BorrowerGroup::class, 'id', 'borrower_group_id');
    }

    public function borrower()
    {
        return $this->hasOne(Borrower::class, 'id', 'borrower_id');
    }

    public function payments()
    {
        return $this->hasMany(LoanRepayment::class, 'borrower_id', 'id');
    }
}
