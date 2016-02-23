<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 22/2/16
 * Time: 10:37 PM
 */

include_once APPLICATION_PATH . '/../vendor/bmob/lib/BmobSms.class.php';

use Phalcon\Cli\Task;


class SmsTask extends Task
{
    public function mainAction()
    {
        echo "\nThis is the default task and the defaffffult action \n";
    }

    /**
     * @param array $params
     */
    public function sendAction()
    {
        $sender = new BmobSms();

        $sender->sendSms("18818655517", "test linkage sms sender");
    }
}