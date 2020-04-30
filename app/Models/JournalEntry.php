<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    protected $table = "journal_entries";

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function chart()
    {
        return $this->hasOne(ChartOfAccount::class, 'id', 'account_id');
    }

    public function valuations()
    {
        return $this->hasMany(AssetValuation::class, 'asset_id', 'id')->orderBy('date', 'desc');
    }
}
