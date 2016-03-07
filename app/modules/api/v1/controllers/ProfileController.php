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

class ProfileController extends APIControllerBase
{
    public function initialize(){
        parent::initialize();
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

        return $this->respondArray($info, 'profile');
    }

    /**
     * @title("modInformation")
     * @description("modify user information")
     * @requestExample("POST /profile/modinformation")
     * @response("Data object or Error object")
     */
    public function modInformationAction(){

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
            $user->updatePasswordByID($cid, $newPassword);

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