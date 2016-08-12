<?php
/**
 * Created by PhpStorm.
 * User: whoami
 * Date: 16-8-4
 * Time: 上午10:30
 */
namespace Multiple\Core\Libraries;

use Phalcon\Di;
use Multiple\Core\Constants\Services;
use Multiple\Core\Constants\ErrorCodes;
use Multiple\Core\Exception\CoreException;
use Multiple\Core\Exception\UserOperationException;

class Alidayu extends \Phalcon\Di\Injectable
{
    private $logger;

    private $redis;

    public function __construct(){
        $this->redis = Di::getDefault()->get(Services::REDIS);
        $this->logger = Di::getDefault()->get(Services::LOGGER);
    }

    public function sendSMS($mobile, $code){
        if(!$mobile){
            throw new UserOperationException(ErrorCodes::USER_MOBILE_NULL,ErrorCodes::$MESSAGE[(ErrorCodes::USER_MOBILE_NULL)]);
        }

        if(!$code){
            throw new UserOperationException(ErrorCodes::USER_VERIFY_CODE_NULL,ErrorCodes::$MESSAGE[(ErrorCodes::USER_VERIFY_CODE_NULL)]);
        }

        if(!$this->redis){
            $this->logger->fatal('No redis.............');
            throw new CoreException(ErrorCodes::GEN_SYSTEM,ErrorCodes::$MESSAGE[(ErrorCodes::GEN_SYSTEM)]);
        }
        else{
            //Sms daemon would read redis consistently to send sms
            $this->redis->lPush('alidayusms', json_encode(array('mobile' => $mobile, 'code' => $code)));
        }
    }

}