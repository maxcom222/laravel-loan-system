<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCheckoutItem extends Model
{
    protected $table = "product_check_out_items";

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    public function items()
    {
        return $this->hasMany(ProductCheckinItem::class, 'product_check_in_id', 'id');
    }
}
