<?php
/**
 * Created by PhpStorm.
 * User: uni
 * Date: 16/2/26
 * Time: 下午10:32
 */

namespace Multiple\Core\Libraries;

use Multiple\Core\Constants\Services;
use Multiple\Core\Constants\ErrorCodes;
use Multiple\Core\Exception\CoreException;
use Multiple\Core\Exception\UserOperationException;

class SMS extends \Phalcon\Di\Injectable
{

    private $redis;

    public function __construct(){
       $this->redis = Di::getDefault()->get(Services::REDIS);
    }

    /**
     * Send SMS
     * @param $mobile
     * @param $msg
     * @throws Exception
     */
    public function send($mobile,$msg){

        if(!$mobile){
            throw new UserOperationException(ErrorCodes::USER_MOBILE_NULL,ErrorCodes::$MESSAGE[(ErrorCodes::USER_MOBILE_NULL)]);
        }

        if(!$msg){
            throw new UserOperationException(ErrorCodes::USER_SMS_CONTENT_NULL,ErrorCodes::$MESSAGE[(ErrorCodes::USER_SMS_CONTENT_NULL)]);
        }

        if(!$this->redis){
            log_message('error','no redis.............');
            throw new CoreException(ErrorCodes::GEN_SYSTEM,ErrorCodes::$MESSAGE[(ErrorCodes::GEN_SYSTEM)]);
        }
        else{
            //Sms daemon would read redis consistently to send sms
            $this->redis->lPush('sms', json_encode(array('mobile' => $mobile, 'msg' => $msg)));
        }

    }

}