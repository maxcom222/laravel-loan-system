<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BranchUser extends Model
{
    protected $table = "branch_users";


    public function branch()
    {
        return $this->hasOne(Branch::class, 'id', 'branch_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

}
