<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetValuation extends Model
{
    protected $table = "asset_valuations";

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    public function asset()
    {
        return $this->hasOne(Asset::class, 'id', 'asset_id');
    }
}
