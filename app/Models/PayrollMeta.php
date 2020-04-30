<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollMeta extends Model
{
    protected $table = "payroll_meta";
    public $timestamps = false;

    public function payroll_template_meta()
    {
        return $this->hasOne(PayrollTemplateMeta::class, 'id', 'payroll_template_meta_id');
    }
}
