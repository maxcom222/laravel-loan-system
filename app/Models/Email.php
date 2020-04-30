<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    protected $table = "emails";

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
