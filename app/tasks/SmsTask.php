<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 22/2/16
 * Time: 10:37 PM
 */

include_once APPLICATION_PATH . '/../vendor/bmob/lib/BmobSms.class.php';

use Phalcon\Cli\Task;
use Phalcon\Di;

use Multiple\Core\Constants\Services;


class SmsTask extends Task
{

    private $redis;

    public function mainAction()
    {
        echo "\nThis is the default task and the default action \n";
    }

    /**
     * @param array $params
     */
    public function sendAction()
    {
        $sender = new BmobSms();

        $this->redis = Di::getDefault()->get(Services::REDIS);

        while (true) {

            $sms_json = $this->redis->rPop('sms');
            if(!$sms_json) {
                sleep(2);
                continue;
            }

            $sms = json_decode($sms_json, true);
            if (!isset($sms['mobile']) || trim($sms['mobile']) == '') {
                echo "Error: mobile number is null \n";
                sleep(2);
                continue;
            }

            $mobile = $sms['mobile'];
            $msg = $sms['msg'];

            echo "send $mobile push message \n";

            $sender->sendSms($mobile, $msg);

            echo "\n\n";
        }
    }
}