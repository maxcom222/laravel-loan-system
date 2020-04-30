<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomFieldMeta extends Model
{
    protected $table = "custom_fields_meta";
    public $timestamps = false;

    public function custom_field()
    {
        return $this->hasOne(CustomField::class, 'id', 'custom_field_id');
    }

}
