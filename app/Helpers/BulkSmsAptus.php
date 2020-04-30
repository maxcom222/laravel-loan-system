<?php
namespace App\Helpers;

use App\Models\Setting;

class BulkSmsAptus
{

//Constructor..
    public function __construct($from, $message, $mobile)
    {

        $mobile_num = $mobile;
        if (is_numeric($mobile_num) == TRUE) {
            $mobile_num = str_replace(' ', '', $mobile_num);
        }
        //REMOVE LEADING ZEROS
        $message = "$message ";

        $username = Setting::where('setting_key', 'bulksms_aptus_username')->first()->setting_value;
        $password = Setting::where('setting_key', 'bulksms_aptus_password')->first()->setting_value;
        $message = urlencode($message);

        $url = "http://www.sms.co.tz/api.php?do=sms&username=$username&password=$password&senderid=$from&msg=$message&dest=$mobile_num";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        $curl_scraped_page = curl_exec($ch);
        curl_close($ch);
    }
}

?>