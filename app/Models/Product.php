<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = "products";

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function categories()
    {
        return $this->hasMany(ProductCategoryMeta::class, 'product_id', 'id');
    }
    public function brand()
    {
        return $this->hasOne(Brand::class, 'id', 'brand_id');
    }

    public function items()
    {
        return $this->hasMany(ProductCheckinItem::class, 'product_check_in_id', 'id');
    }
}
