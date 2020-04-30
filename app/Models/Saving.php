<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Saving extends Model
{
    protected $table = "savings";


    public function saving_transactions()
    {
        return $this->hasMany(SavingTransaction::class, 'savings_id', 'id');
    }

    public function borrower()
    {
        return $this->hasOne(Borrower::class, 'id', 'borrower_id');
    }

    public function savings_product()
    {
        return $this->hasOne(SavingProduct::class, 'id', 'savings_product_id');
    }


}
