<?php
/**
 * Created by PhpStorm.
 * User: uni
 * Date: 16/2/26
 * Time: 下午6:30
 */

namespace Multiple\API\Controllers;

use Phalcon\Di;
use Phalcon\Db\RawValue;

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

    public function initialize(){
        parent::initialize();

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

        $companyType = $ctype == '0' ? LinkageUtils::COMPANY_MANUFACTURE : LinkageUtils::COMPANY_TRANSPORTER;
        $role = $ctype == '1' ? LinkageUtils::USER_ADMIN_MANUFACTURE : LinkageUtils::USER_ADMIN_TRANSPORTER;
        try{
            // Start a transaction
            $this->db->begin();

            $company = new Company();
            $company->create($companyName,$companyType);
            $companyID = $company->company_id;

            $user = new ClientUser();
            $user->registerByMobile($mobile, $password, StatusCodes::CLIENT_USER_PENDING, $companyID);
            $userID = $user->user_id;

            $userRole = new ClientUserRole();
            $userRole->create($userID, $role);

            // Commit the transaction
            $this->db->commit();

        }catch (Exception $e){
            $this->db->rollback();

            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondOK();
    }

    /**
     * @title("login")
     * @description("User login")
     * @requestExample("POST /session/login")
     * @response("Data object or Error object")
     */
    public function loginAction(){

    }

    /**
     * @title("forgot password")
     * @description("User forgot password")
     * @requestExample("POST /session/forgotpassword")
     * @response("Data object or Error object")
     */
    public function forgotPasswordAction(){

    }
}