<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $table = "branches";

    public $timestamps = false;
    public function users()
    {
        return $this->hasMany(BranchUser::class, 'branch_id', 'id');
    }
}
