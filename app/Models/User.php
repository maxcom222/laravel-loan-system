<?php

namespace App\Models;

use Cartalyst\Sentinel\Users\EloquentUser;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends EloquentUser
{

    protected $fillable = [
        'email',
        'password',
        'last_name',
        'first_name',
        'permissions',
        'address',
        'notes',
        'phone',
        'gender'
    ];
    public function payroll()
    {
        return $this->hasMany(Payroll::class, 'user_id', 'id');
    }
}
