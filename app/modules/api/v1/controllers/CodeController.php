<?php
/**
 * Created by PhpStorm.
 * User: uni
 * Date: 16/2/26
 * Time: 下午7:55
 */


namespace Multiple\API\Controllers;

use Phalcon\Di;

use Multiple\Core\Exception\Exception;
use Multiple\Core\APIControllerBase;
use Multiple\Core\Constants\ErrorCodes;
use Multiple\Core\Constants\LinkageUtils;
use Multiple\Core\Constants\Services;

use Multiple\Models\ClientUser;

class CodeController extends APIControllerBase
{
    private $redis;

    private $logger;

    private $sms;

    public function initialize(){
        parent::initialize();

        $this->redis = Di::getDefault()->get(Services::REDIS);
        $this->logger = Di::getDefault()->get(Services::LOGGER);
        $this->sms = Di::getDefault()->get(Services::SMS);
    }

    /**
     * @title("verifyCode")
     * @description("Get password verify code")
     * @requestExample("POST /code/verifycode")
     * @response("Data object or Error object")
     */
    public function verifycodeAction(){
        $mobile = $this->request->getPost('mobile');

        if(!isset($mobile)){
            return $this->respondError(ErrorCodes::USER_MOBILE_NULL, ErrorCodes::$MESSAGE[ErrorCodes::USER_MOBILE_NULL]);
        }

        try{
            $key = LinkageUtils::VERIFY_PREFIX.$mobile;
            $expire = 60;
            $verify_code =  rand(1000, 9999);

            $msg = "［］您的校验码是：".$verify_code."。1分钟内有效。如非本人操作忽略此短信。";

            //如果客户端多次调用接口生成校验码，以最后一次校验码为准
            $this->redis->setex($key, $expire, $verify_code);

            //send message
            $this->sms->send($mobile, $msg);

        }catch (Exception $e){
            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondOK();

    }

    /**
     * @title("invitecode")
     * @description("Get user invite code")
     * @requestExample("POST /code/invitecode")
     * @response("Data object or Error object")
     */
    public function invitecodeAction(){
        $mobile = $this->request->getPost('mobile');

        if(!isset($this->cid)){
            return $this->respondError(ErrorCodes::USER_ID_NULL, ErrorCodes::$MESSAGE[ErrorCodes::USER_ID_NULL]);
        }

        if(!isset($mobile)){
            return $this->respondError(ErrorCodes::USER_MOBILE_NULL, ErrorCodes::$MESSAGE[ErrorCodes::USER_MOBILE_NULL]);
        }

        try{
            $expire = 60 * 60 * 24;

            $user = new ClientUser();
            $companyID = $user->getCompanyidByUserid($this->cid);
            $userName = $user->getUserNameByUserid($this->cid);
            $inviteCode = $this->genInviteCode($companyID);

            //如果客户端多次调用接口生成校验码，以最后一次校验码为准
            $this->redis->setex($inviteCode, $expire, $companyID);

            $msg = $userName."邀请你注册Linkage。您的邀请码是：".$inviteCode;

            //send message
            $this->sms->send($mobile, $msg);

        }catch (Exception $e){
            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondOK();
    }

    /**
     * @title("inviteurl")
     * @description("Get user invite URL")
     * @requestExample("POST /code/inviteurl")
     * @response("Data object or Error object")
     */
    public function inviteurlAction(){
        if(!isset($this->cid)){
            return $this->respondError(ErrorCodes::USER_ID_NULL, ErrorCodes::$MESSAGE[ErrorCodes::USER_ID_NULL]);
        }

        $url = '';
        try{
            $expire = 60 * 60 * 24;

            $user = new ClientUser();
            $companyID = $user->getCompanyidByUserid($this->cid);
            $CN = $this->genInviteCode($companyID);

            //如果客户端多次调用接口生成校验码，以最后一次校验码为准
            $this->redis->setex($CN, $expire, $companyID);

            $url = LinkageUtils::LINKAGE_SERVER.'/register?cn='.$CN;

        }catch (Exception $e){
            return $this->respondError($e->getCode(), $e->getMessage());
        }

        $response = [
            'URL' => $url,
        ];

        return $this->respondData($response);

    }


    private function genInviteCode($company_id){
        return LinkageUtils::INVITE_SECRET + (int)$company_id;
    }

}