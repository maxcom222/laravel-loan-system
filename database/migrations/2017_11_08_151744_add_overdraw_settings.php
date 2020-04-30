<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOverdrawSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::table('settings')->insert([
            [
                'setting_key' => 'allow_bank_overdraw',
                'setting_value' => '0',
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        App\Models\Setting::where('setting_key', 'allow_bank_overdraw')
            ->delete();

    }
}
