<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BorrowerGroup extends Model
{
    protected $table = "borrower_groups";

    public function members()
    {
        return $this->hasMany(BorrowerGroupMember::class, 'borrower_group_id', 'id');
    }
}
