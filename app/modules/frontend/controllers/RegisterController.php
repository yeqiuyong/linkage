<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 4/2/16
 * Time: 4:25 PM
 */

namespace Multiple\Frontend\Controllers;

use Phalcon\Di;

use Multiple\Core\FrontendControllerBase;
use Multiple\Core\Exception\Exception;
use Multiple\Core\Constants\Services;
use Multiple\Core\Constants\ErrorCodes;
use Multiple\Core\Constants\StatusCodes;
use Multiple\Core\Constants\LinkageUtils;

use Multiple\Models\Company;
use Multiple\Models\ClientUser;
use Multiple\Models\ClientUserRole;


/**
 * SessionController
 *
 * Allows to register new users
 */
class RegisterController extends FrontendControllerBase
{

    private $redis;

    private $sms;

    public function initialize()
    {
        $this->tag->setTitle('Sign Up/Sign In');
        parent::initialize();

        $this->redis = Di::getDefault()->get(Services::REDIS);
        $this->sms = Di::getDefault()->get(Services::SMS);
    }

    /**
     * Action to register a new user
     */
    public function indexAction(){
        $cn = $this->request->get("cn");

        $this->view->setVar(
            "cn",
            $cn
        );
    }

    public function registerAction(){
        $cn = $this->request->getPost('cn');
        $mobile = $this->request->getPost('mobile');
        $password = $this->request->getPost('password');
        $ctype = $this->request->getPost('ctype');
        $verifyCode = $this->request->getPost('verify_code');

        if(!isset($cn)){
            return $this->responseJsonError(ErrorCodes::AUTH_BADTOKEN, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_BADTOKEN]);
        }

        if(!isset($mobile)){
            return $this->responseJsonError(ErrorCodes::USER_MOBILE_NULL, ErrorCodes::$MESSAGE[ErrorCodes::USER_MOBILE_NULL]);
        }

        if(!isset($password)){
            return $this->responseJsonError(ErrorCodes::USER_PASSWORD_NULL, ErrorCodes::$MESSAGE[ErrorCodes::USER_PASSWORD_NULL]);
        }

        if(!isset($ctype)){
            return $this->responseJsonError(ErrorCodes::USER_ROLE_NULL, ErrorCodes::$MESSAGE[ErrorCodes::USER_ROLE_NULL]);
        }

        if(!isset($verifyCode)){
            return $this->responseJsonError(ErrorCodes::USER_VERIFY_CODE_NULL, ErrorCodes::$MESSAGE[ErrorCodes::USER_VERIFY_CODE_NULL]);
        }

        $key = LinkageUtils::VERIFY_PREFIX.$mobile;
        if(!$this->redis->get($key)){
            return $this->responseJsonError(ErrorCodes::USER_INVITE_CODE_EXPIRE, ErrorCodes::$MESSAGE[ErrorCodes::USER_INVITE_CODE_EXPIRE]);
        }

        if($verifyCode != $this->redis->get($key)){
            return $this->responseJsonError(ErrorCodes::USER_VERIFY_CODE_ERROR, ErrorCodes::$MESSAGE[ErrorCodes::USER_VERIFY_CODE_ERROR]);
        }

        switch($ctype){
            case '0': $role = LinkageUtils::USER_MANUFACTURE;break;
            case '1': $role = LinkageUtils::USER_TRANSPORTER;break;
            case '2': $role = LinkageUtils::USER_DRIVER;break;
            default:
                return $this->responseJsonError(ErrorCodes::USER_TYPE_ERROR, ErrorCodes::$MESSAGE[ErrorCodes::USER_TYPE_ERROR]);
        }

        try{
            // Start a transaction
            $this->db->begin();

            $companyID = (int)$cn - LinkageUtils::INVITE_SECRET;
            return $this->responseJsonError(ErrorCodes::COMPANY_NOTFOUND, $companyID);
            $company = new Company();
            if($company->isCompanyExist($companyID)){
                return $this->responseJsonError(ErrorCodes::COMPANY_NOTFOUND, ErrorCodes::$MESSAGE[ErrorCodes::COMPANY_NOTFOUND]);
            }

            $user = new ClientUser();
            $user->registerByMobile($mobile, $password, StatusCodes::CLIENT_USER_ACTIVE, $companyID);
            $userID = $user->user_id;

            $userRole = new ClientUserRole();
            $userRole->add($userID, $role);

            // Commit the transaction
            $this->db->commit();

        }catch (Exception $e){
            $this->db->rollback();

            return $this->responseJsonError($e->getCode(), $e->getMessage());
        }

        $downloadURL = ['url' => LinkageUtils::APP_DOWNLOAD_URL];
        return $this->responseJsonData($downloadURL);
    }

    public function verifycodeAction(){
        $mobile = $this->request->getPost('mobile');

        if(!isset($mobile)){
            return $this->responseJsonError(ErrorCodes::USER_MOBILE_NULL, ErrorCodes::$MESSAGE[ErrorCodes::USER_MOBILE_NULL]);
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
            return $this->responseJsonError($e->getCode(), $e->getMessage());
        }

        return $this->responseJsonOK();

    }

}
