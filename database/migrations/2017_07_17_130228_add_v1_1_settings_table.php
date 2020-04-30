<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddV11SettingsTable extends Migration
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
                'setting_key' => 'allow_self_registration',
                'setting_value' => '0',
            ],
            [
                'setting_key' => 'allow_client_login',
                'setting_value' => '0',
            ],
            [
                'setting_key' => 'welcome_note',
                'setting_value' => 'Welcome to our company. You can login with your username and password',
            ],
            [
                'setting_key' => 'allow_client_apply',
                'setting_value' => '0',
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        App\Models\Setting::where('setting_key', 'allow_self_registration')
            ->delete();
        App\Models\Setting::where('setting_key', 'allow_client_login')
            ->delete();
        App\Models\Setting::where('setting_key', 'welcome_note')
            ->delete();
        App\Models\Setting::where('setting_key', 'allow_client_apply')
            ->delete();
    }
}
