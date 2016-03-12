<?php
/**
 * Created by PhpStorm.
 * User: uni
 * Date: 16/2/26
 * Time: 下午6:30
 */

namespace Multiple\API\Controllers;

use Phalcon\Di;

use Multiple\Core\Auth\MobileAdaptor;
use Multiple\Core\APIControllerBase;
use Multiple\Core\Constants\Services;
use Multiple\Core\Constants\ErrorCodes;
use Multiple\Core\Constants\StatusCodes;
use Multiple\Core\Constants\LinkageUtils;
use Multiple\Core\Exception\Exception;

use Multiple\Models\Company;
use Multiple\Models\ClientUser;
use Multiple\Models\ClientUserRole;

class SessionController extends APIControllerBase
{
    private $logger;

    private $redis;

    public function initialize(){
        parent::initialize();

        $this->redis = Di::getDefault()->get(Services::REDIS);
        $this->logger = Di::getDefault()->get(Services::LOGGER);
    }

    /**
     * @title("register4admin")
     * @description("Admin User registration")
     * @requestExample("POST /session/register4admin")
     * @response("Data object or Error object")
     */
    public function register4adminAction(){
        $mobile = $this->request->getPost('mobile');
        $password = $this->request->getPost('password');
        $ctype = $this->request->getPost('ctype');
        $verifyCode = $this->request->getPost('verify_code');
        $companyName = $this->request->getPost('company_name');

        if(!isset($mobile)){
            return $this->respondError(ErrorCodes::USER_MOBILE_NULL, ErrorCodes::$MESSAGE[ErrorCodes::USER_MOBILE_NULL]);
        }

        if(!isset($password)){
            return $this->respondError(ErrorCodes::USER_PASSWORD_NULL, ErrorCodes::$MESSAGE[ErrorCodes::USER_PASSWORD_NULL]);
        }

        if(!isset($ctype)){
            return $this->respondError(ErrorCodes::USER_ROLE_NULL, ErrorCodes::$MESSAGE[ErrorCodes::USER_ROLE_NULL]);
        }

        if(!isset($verifyCode)){
            return $this->respondError(ErrorCodes::USER_VERIFY_CODE_NULL, ErrorCodes::$MESSAGE[ErrorCodes::USER_VERIFY_CODE_NULL]);
        }

        if(!isset($companyName)){
            return $this->respondError(ErrorCodes::USER_COMPANY_NAME_NULL, ErrorCodes::$MESSAGE[ErrorCodes::USER_COMPANY_NAME_NULL]);
        }

        $key = LinkageUtils::VERIFY_PREFIX.$mobile;
        if(!$this->redis->get($key)){
            return $this->respondError(ErrorCodes::USER_VERIFY_CODE_EXPIRE, ErrorCodes::$MESSAGE[ErrorCodes::USER_VERIFY_CODE_EXPIRE]);
        }else{
            $code = $this->redis->get($key);
            if($code != $verifyCode){
                return $this->respondError(ErrorCodes::USER_VERIFY_CODE_ERROR, ErrorCodes::$MESSAGE[ErrorCodes::USER_VERIFY_CODE_ERROR]);
            }
        }

        $companyType = $ctype == '0' ? LinkageUtils::COMPANY_MANUFACTURE : LinkageUtils::COMPANY_TRANSPORTER;
        $role = $ctype == '1' ? LinkageUtils::USER_ADMIN_MANUFACTURE : LinkageUtils::USER_ADMIN_TRANSPORTER;
        try{
            // Start a transaction
            $this->db->begin();

            $company = new Company();
            $company->add($companyName, $companyType);
            $companyID = $company->company_id;

            $user = new ClientUser();
            $user->registerByMobile($mobile, $password, StatusCodes::CLIENT_USER_PENDING, $companyID);
            $userID = $user->user_id;

            $userRole = new ClientUserRole();
            $userRole->add($userID, $role);

            // Commit the transaction
            $this->db->commit();

        }catch (Exception $e){
            $this->db->rollback();

            return $this->respondError($e->getCode(), $e->getMessage());
        }

        $response = $this->getTokenResponse($userID, $mobile, $password);

        return $this->respondArray($response);

    }

    /**
     * @title("register4invitecode")
     * @description("User registration")
     * @requestExample("POST /session/register4invitecode")
     * @response("Data object or Error object")
     */
    public function register4invitecodeAction(){
        $mobile = $this->request->getPost('mobile');
        $password = $this->request->getPost('password');
        $ctype = $this->request->getPost('ctype');
        $inviteCode = $this->request->getPost('invite_code');

        if(!isset($mobile)){
            return $this->respondError(ErrorCodes::USER_MOBILE_NULL, ErrorCodes::$MESSAGE[ErrorCodes::USER_MOBILE_NULL]);
        }

        if(!isset($password)){
            return $this->respondError(ErrorCodes::USER_PASSWORD_NULL, ErrorCodes::$MESSAGE[ErrorCodes::USER_PASSWORD_NULL]);
        }

        if(!isset($ctype)){
            return $this->respondError(ErrorCodes::USER_ROLE_NULL, ErrorCodes::$MESSAGE[ErrorCodes::USER_ROLE_NULL]);
        }

        if(!isset($inviteCode)){
            return $this->respondError(ErrorCodes::USER_INVITE_CODE_NULL, ErrorCodes::$MESSAGE[ErrorCodes::USER_INVITE_CODE_NULL]);
        }

        if(!$this->redis->get($inviteCode)){
            return $this->respondError(ErrorCodes::USER_INVITE_CODE_EXPIRE, ErrorCodes::$MESSAGE[ErrorCodes::USER_INVITE_CODE_EXPIRE]);
        }

        switch($ctype){
            case '0': $role = LinkageUtils::USER_MANUFACTURE;break;
            case '1': $role = LinkageUtils::USER_TRANSPORTER;break;
            case '2': $role = LinkageUtils::USER_DRIVER;break;
            default:
                return $this->respondError(ErrorCodes::USER_TYPE_ERROR, ErrorCodes::$MESSAGE[ErrorCodes::USER_TYPE_ERROR]);
        }

        try{
            // Start a transaction
            $this->db->begin();

            $companyID = $this->redis->get($inviteCode);
            $company = new Company();
            if(!$company->isCompanyExist($companyID)){
                return $this->respondError(ErrorCodes::COMPANY_NOTFOUND, ErrorCodes::$MESSAGE[ErrorCodes::COMPANY_NOTFOUND]);
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

            return $this->respondError($e->getCode(), $e->getMessage());
        }

        $response = $this->getTokenResponse($userID, $mobile, $password);

        return $this->respondArray($response);

    }

    /**
     * @title("login")
     * @description("User login")
     * @requestExample("POST /session/login")
     * @response("Data object or Error object")
     */
    public function loginAction(){
        $mobile = $this->request->getPost('mobile');
        $password = $this->request->getPost('password');

        try {
            $authManager = $this->di->get(Services::AUTH_MANAGER);
            $session = $authManager->loginWithMobilePassword(MobileAdaptor::NAME, $mobile, $password);
            $userid = $session->getIdentity();

            $response = $this->getTokenResponse($userid, $mobile, $password);

        }catch (Exception $e){
            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondArray($response);
    }

    /**
     * @title("forgot password")
     * @description("User forgot password")
     * @requestExample("POST /session/forgotpassword")
     * @response("Data object or Error object")
     */
    public function forgotPasswordAction(){
        $mobile = $this->request->getPost('mobile');
        $password = $this->request->getPost('password');
        $verifyCode = $this->request->getPost('verify_code');

        if(!isset($mobile)){
            return $this->respondError(ErrorCodes::USER_MOBILE_NULL, ErrorCodes::$MESSAGE[ErrorCodes::USER_MOBILE_NULL]);
        }

        if(!isset($password)){
            return $this->respondError(ErrorCodes::USER_PASSWORD_NULL, ErrorCodes::$MESSAGE[ErrorCodes::USER_PASSWORD_NULL]);
        }

        if(!isset($verifyCode)){
            return $this->respondError(ErrorCodes::USER_VERIFY_CODE_NULL, ErrorCodes::$MESSAGE[ErrorCodes::USER_VERIFY_CODE_NULL]);
        }

        $key = LinkageUtils::VERIFY_PREFIX.$mobile;
        if(!$this->redis->get($key)){
            return $this->respondError(ErrorCodes::USER_VERIFY_CODE_EXPIRE, ErrorCodes::$MESSAGE[ErrorCodes::USER_VERIFY_CODE_EXPIRE]);
        }else{
            $code = $this->redis->get($key);
            if($code != $verifyCode){
                return $this->respondError(ErrorCodes::USER_VERIFY_CODE_ERROR, ErrorCodes::$MESSAGE[ErrorCodes::USER_VERIFY_CODE_ERROR]);
            }
        }

        try{
            $user = new ClientUser();
            $user->updatePasswordByMobile($mobile, $password);

        }catch (Exception $e){
            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondOK();

    }


    private function getTokenResponse($cid, $mobile, $password){
        $authManager = $this->di->get(Services::AUTH_MANAGER);
        $session = $authManager->loginWithMobilePassword(MobileAdaptor::NAME, $mobile, $password);
        $response = [
            'cid' => $cid,
            'token' => $session->getToken(),
            'expires' => $session->getExpirationTime()
        ];

        return $response;
    }
}