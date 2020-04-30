<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sms extends Model
{
    protected $table = "sms";

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
