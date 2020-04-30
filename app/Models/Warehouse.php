<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $table = "warehouses";

    public $timestamps = false;

    public function assets()
    {
        return $this->hasMany(Asset::class, 'asset_type_id', 'id');
    }
}
