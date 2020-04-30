<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPayment extends Model
{
    protected $table = "product_payments";

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function product_check_in()
    {
        return $this->hasOne(ProductCheckin::class, 'id', 'product_check_in_id');
    }

    public function product_check_out()
    {
        return $this->hasOne(ProductCheckin::class, 'id', 'product_check_out_id');
    }

    public function bank()
    {
        return $this->hasOne(BankAccount::class, 'id', 'bank_id');
    }

    public function payment_method()
    {
        return $this->hasOne(LoanRepaymentMethod::class, 'id', 'payment_method_id');
    }

}
