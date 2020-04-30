<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCheckin extends Model
{
    protected $table = "product_check_ins";

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function supplier()
    {
        return $this->hasOne(Supplier::class, 'id', 'supplier_id');
    }

    public function items()
    {
        return $this->hasMany(ProductCheckinItem::class, 'product_check_in_id', 'id');
    }
    public function payments()
    {
        return $this->hasMany(ProductPayment::class, 'product_check_in_id', 'id')->orderBy('date','desc');
    }
}
