<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Add20Settings extends Migration
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
                'setting_key' => 'default_online_payment_method',
                'setting_value' => '',
            ],
            [
                'setting_key' => 'timezone',
                'setting_value' => 'Africa/Blantyre',
            ],
            [
                'setting_key' => 'auto_download_update',
                'setting_value' => '0',
            ],
            [
                'setting_key' => 'update_notification',
                'setting_value' => '',
            ],
            [
                'setting_key' => 'update_last_checked',
                'setting_value' => '',
            ],
            [
                'setting_key' => 'header_javascript',
                'setting_value' => '',
            ],
            [
                'setting_key' => 'footer_javascript',
                'setting_value' => '',
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
        App\Models\Setting::where('setting_key', 'default_online_payment_method')
            ->delete();
        App\Models\Setting::where('setting_key', 'timezone')
            ->delete();
        App\Models\Setting::where('setting_key', 'auto_download_update')
            ->delete();
        App\Models\Setting::where('setting_key', 'update_notification')
            ->delete();
        App\Models\Setting::where('setting_key', 'update_last_checked')
            ->delete();
        App\Models\Setting::where('setting_key', 'header_javascript')
            ->delete();
        App\Models\Setting::where('setting_key', 'footer_javascript')
            ->delete();
    }
}
