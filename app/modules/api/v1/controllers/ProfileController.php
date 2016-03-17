<?php
/**
 * Created by PhpStorm.
 * User: uni
 * Date: 16/2/26
 * Time: 下午6:54
 */

namespace Multiple\API\Controllers;

use Multiple\Core\Exception\Exception;
use Multiple\Core\APIControllerBase;
use Multiple\Core\Constants\ErrorCodes;
use Multiple\Models\ClientUser;
use Multiple\Models\Company;

class ProfileController extends APIControllerBase
{
    public function initialize(){
        parent::initialize();
    }

    /**
     * @title("main")
     * @description("user main page")
     * @requestExample("POST /profile/main")
     * @response("Data object or Error object")
     */
    public function mainAction(){
        if(!isset($this->cid)){
            return $this->respondError(ErrorCodes::AUTH_IDENTITY_MISS, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_IDENTITY_MISS]);
        }

        try{
            $user = new ClientUser();
            $userInfo = $user->getUserInfomation($this->cid);
            $isAdmin =$user->isAdmin($this->cid) ? '1' : '0';

            $company = new Company();
            $companyInfo = $company->getCompanyInformation($userInfo['company_id']);

            $result = [
              'company_name' => $companyInfo['name'],
                'company_icon' => $companyInfo['logo'],
                'username' => isset($userInfo['username']) ? $userInfo['username'] : '',
                'realname' => isset($userInfo['realname']) ? $userInfo['realname'] : '',
                'mobile' => $userInfo['mobile'],
                'user_icon' => $userInfo['icon'],
                'is_admin' => $isAdmin,
            ];

        }catch (Exception $e){
            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondArray($result);
    }

    /**
     * @title("information")
     * @description("user information")
     * @requestExample("POST /profile/information")
     * @response("Data object or Error object")
     */
    public function informationAction(){
        if(!isset($this->cid)){
            return $this->respondError(ErrorCodes::AUTH_IDENTITY_MISS, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_IDENTITY_MISS]);
        }

        try{
            $user = new ClientUser();
            $info = $user->getUserInfomation($this->cid);

        }catch (Exception $e){
            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondArray($info);
    }

    /**
     * @title("modInformation")
     * @description("modify user information")
     * @requestExample("POST /profile/modinformation")
     * @response("Data object or Error object")
     */
    public function modInformationAction(){
        if(!isset($this->cid)){
            return $this->respondError(ErrorCodes::AUTH_IDENTITY_MISS, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_IDENTITY_MISS]);
        }

        $username = $this->request->getPost('username');
        $name = $this->request->getPost('realname');
        $email = $this->request->getPost('email');
        $gender = $this->request->getPost('gender');
        $birthday = $this->request->getPost('birthday');
        $identity = $this->request->getPost('identity');

        $info = [
            'username' => $username,
            'name' =>$name,
            'email' => $email,
            'gender' => $gender,
            'birthday' => $birthday,
            'identity' => $identity,
        ];

        try{
            $user = new ClientUser();
            $user->updateProfile($this->cid, $info);
        }catch (Exception $e){
            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondOK();

    }

    /**
     * @title("modPassword")
     * @description("Modify Password")
     * @requestExample("POST /profile/modpassword")
     * @response("Data object or Error object")
     */
    public function modPasswordAction(){
        $newPassword = $this->request->getPost('new_password');
        $oldPassword = $this->request->getPost('old_password');

        if(!isset($this->cid)){
            return $this->respondError(ErrorCodes::AUTH_IDENTITY_MISS, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_IDENTITY_MISS]);
        }

        if(!isset($newPassword) || !isset($oldPassword)){
            return $this->respondError(ErrorCodes::USER_PASSWORD_NULL, ErrorCodes::$MESSAGE[ErrorCodes::USER_PASSWORD_NULL]);
        }

        try{
            $user = new ClientUser();

            if(!$user->isPasswordValidate($this->cid, $oldPassword)){
                return $this->respondError(ErrorCodes::AUTH_PASSWORD_INVALID, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_PASSWORD_INVALID]);
            }

            $user->updatePasswordByID($this->cid, $newPassword);

        }catch (Exception $e){
            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondOK();
    }

    /**
     * @title("modmobile")
     * @description("Modify Mobile")
     * @requestExample("POST /profile/modmobile")
     * @response("Data object or Error object")
     */
    public function modMobileAction(){
        $mobile = $this->request->getPost('mobile');

        if(!isset($this->cid)){
            return $this->respondError(ErrorCodes::AUTH_IDENTITY_MISS, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_IDENTITY_MISS]);
        }

        if(!isset($newPassword) || !isset($oldPassword)){
            return $this->respondError(ErrorCodes::USER_PASSWORD_NULL, ErrorCodes::$MESSAGE[ErrorCodes::USER_PASSWORD_NULL]);
        }

        try{
            $user = new ClientUser();
            $user->updatePasswordByID($this->cid, $newPassword);

        }catch (Exception $e){
            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondOK();
    }

    /**
     * @title("addCompany")
     * @description("Add user company")
     * @requestExample("POST /profile/addcompany")
     * @response("Data object or Error object")
     */
    public function addCompanyAction(){

    }

    /**
     * @title("modCompany")
     * @description("Modify user company")
     * @requestExample("POST /profile/forgotpassword")
     * @response("Data object or Error object")
     */
    public function modCompanyAction(){

    }



}