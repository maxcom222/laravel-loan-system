<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    protected $table = "product_categories";

    public $timestamps = false;

    public function products()
    {
        return $this->hasMany(ProductCategoryMeta::class, 'product_category_id', 'id');
    }
}
