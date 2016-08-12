<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 22/2/16
 * Time: 10:37 PM
 */

require APPLICATION_PATH . '/../app/core/libraries/Alidayu/TopSdk.php';
require APPLICATION_PATH . '/../app/core/libraries/Alidayu/top/TopClient.php';
require APPLICATION_PATH . '/../app/core/libraries/Alidayu/top/request/AlibabaAliqinFcSmsNumSendRequest.php';
date_default_timezone_set('Asia/Shanghai');

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

            $c = new \TopClient;
            $c->appkey = '23428422';
            $c->secretKey = 'baa9273882d94b1a7caad3e396341751';
            $req = new \AlibabaAliqinFcSmsNumSendRequest;
            $req->setExtend("123456");
            $req->setSmsType("normal");
            $req->setSmsFreeSignName("领骐物流");
            $req->setSmsParam("{\"code\":\"$msg\"}");
            $req->setRecNum($mobile);
            $req->setSmsTemplateCode("SMS_12991382");
            $c->execute($req);

            echo "\n\n";
        }
    }
}