<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCategoryMeta extends Model
{
    protected $table = "product_categories_meta";

    public $timestamps = false;

    public function category()
    {
        return $this->hasOne(ProductCategory::class, 'id', 'product_category_id');
    }
}
