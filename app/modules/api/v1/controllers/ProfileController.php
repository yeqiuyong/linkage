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
use Multiple\Models\Favorite;
use Multiple\Models\UserAddress;
use Multiple\Models\SystemSet;

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
                'user_icon' => isset($userInfo['icon']) ? $userInfo['icon'] : '',
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
            $userInfo = $user->getUserInfomation($this->cid);

            unset($userInfo['role_id']);
            unset($userInfo['status']);

        }catch (Exception $e){
            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondArray($userInfo);
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

        $username = $this->request->getPost('username', 'string');
        $name = $this->request->getPost('realname', 'string');
        $email = $this->request->getPost('email', 'string');
        $gender = $this->request->getPost('gender', 'string');
        $birthday = $this->request->getPost('birthday', 'int');
        $identity = $this->request->getPost('identity', 'string');

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

        if(!isset($mobile)){
            return $this->respondError(ErrorCodes::USER_MOBILE_NULL, ErrorCodes::$MESSAGE[ErrorCodes::USER_MOBILE_NULL]);
        }

        try{
            $user = new ClientUser();
            $user->updateMobileByID($this->cid, $mobile);

        }catch (Exception $e){
            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondOK();
    }


    /**
     * @title("favlist")
     * @description("User favorite")
     * @requestExample("POST /profile/favlist")
     * @response("Data object or Error object")
     */
    public function favlistAction(){
        $pagination = $this->request->getPost('pagination', 'int');
        $offset = $this->request->getPost('offset', 'int');
        $size = $this->request->getPost('size', 'int');

        if(!isset($this->cid)){
            return $this->respondError(ErrorCodes::AUTH_IDENTITY_MISS, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_IDENTITY_MISS]);
        }

        try {
            $favorite = new Favorite();
            $myFavorites = $favorite->getList($this->cid, $pagination, $offset, $size);

        }catch (Exception $e){
            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondArray(['companies' => $myFavorites]);

    }

    /**
     * @title("addfavorite")
     * @description("User favirate")
     * @requestExample("POST /profile/favlist")
     * @response("Data object or Error object")
     */
    public function addFavoriteAction(){
        $companyId = $this->request->getPost('company_id', 'int');

        if(!isset($this->cid)){
            return $this->respondError(ErrorCodes::AUTH_IDENTITY_MISS, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_IDENTITY_MISS]);
        }

        if(empty($companyId)){
            return $this->respondError(ErrorCodes::COMPANY_ID_NULL, ErrorCodes::$MESSAGE[ErrorCodes::COMPANY_ID_NULL]);
        }


        try {
            $favorite = new Favorite();
            $isfavorite = $favorite->isFavorite($this->cid,$companyId);
            if($isfavorite){
                $favorite->add($this->cid, $companyId);
            }else{
                return $this->respondError(ErrorCodes::USER_FAVORITE_EXIST, ErrorCodes::$MESSAGE[ErrorCodes::USER_FAVORITE_EXIST]);
            }

        }catch (Exception $e){
            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondOK();

    }

    /**
     * @title("addfavorite")
     * @description("User favirate")
     * @requestExample("POST /profile/favlist")
     * @response("Data object or Error object")
     */
    public function delFavoriteAction(){
        $companyId = $this->request->getPost('company_id', 'int');

        if(!isset($this->cid)){
            return $this->respondError(ErrorCodes::AUTH_IDENTITY_MISS, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_IDENTITY_MISS]);
        }

        if(empty($companyId)){
            return $this->respondError(ErrorCodes::COMPANY_ID_NULL, ErrorCodes::$MESSAGE[ErrorCodes::COMPANY_ID_NULL]);
        }

        try {
            $favorite = new Favorite();
            $favorite->delFavorite($this->cid, $companyId);

        }catch (Exception $e){
            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondOK();

    }


    /**
     * @title("addrlist")
     * @description("User address")
     * @requestExample("POST /profile/addrlist")
     * @response("Data object or Error object")
     */
    public function addrlistAction(){
        $pagination = $this->request->getPost('pagination', 'int');
        $offset = $this->request->getPost('offset', 'int');
        $size = $this->request->getPost('size', 'int');

        if(!isset($this->cid)){
            return $this->respondError(ErrorCodes::AUTH_IDENTITY_MISS, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_IDENTITY_MISS]);
        }

        try {
            $userAddress = new UserAddress();
            $myAddresses = $userAddress->getList($this->cid, $pagination, $offset, $size);

        }catch (Exception $e){
            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondArray(['addresses' => $myAddresses]);

    }

    /**
     * @title("addAddr")
     * @description("User Address")
     * @requestExample("POST /profile/addAddr")
     * @response("Data object or Error object")
     */
    public function addAddrAction(){
        $title = $this->request->getPost('title', 'string');
        $address = $this->request->getPost('address', 'string');

        if(!isset($this->cid)){
            return $this->respondError(ErrorCodes::AUTH_IDENTITY_MISS, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_IDENTITY_MISS]);
        }

        if(empty($title) || empty($address)){
            return $this->respondError(ErrorCodes::USER_ADDRESS_INPUT_ERROR, ErrorCodes::$MESSAGE[ErrorCodes::USER_ADDRESS_INPUT_ERROR]);
        }

        try {
            $mAddress = new UserAddress();
            $address_id = $mAddress->add($this->cid, $title, $address);

        }catch (Exception $e){
            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondArray(['address_id' => $address_id]);

    }

    /**
     * @title("addfavorite")
     * @description("User favirate")
     * @requestExample("POST /profile/favlist")
     * @response("Data object or Error object")
     */
    public function delAddrAction(){
        $addressId = $this->request->getPost('address_id', 'int');

        if(!isset($this->cid)){
            return $this->respondError(ErrorCodes::AUTH_IDENTITY_MISS, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_IDENTITY_MISS]);
        }

        if(empty($addressId)){
            return $this->respondError(ErrorCodes::USER_ADDRESS_INPUT_ERROR, ErrorCodes::$MESSAGE[ErrorCodes::USER_ADDRESS_INPUT_ERROR]);
        }

        try {
            $mAddress = new UserAddress();
            $mAddress->delAddress($this->cid, $addressId);

        }catch (Exception $e){
            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondOK();

    }


    /**
     * @title("addfavorite")
     * @description("User favirate")
     * @requestExample("POST /profile/favlist")
     * @response("Data object or Error object")
     */
    public function modAddrAction(){
        $addressId = $this->request->getPost('address_id', 'int');
        $title = $this->request->getPost('title', 'string');
        $address = $this->request->getPost('address', 'string');

        if(!isset($this->cid)){
            return $this->respondError(ErrorCodes::AUTH_IDENTITY_MISS, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_IDENTITY_MISS]);
        }

        if(empty($addressId)){
            return $this->respondError(ErrorCodes::USER_ADDRESS_INPUT_ERROR, ErrorCodes::$MESSAGE[ErrorCodes::USER_ADDRESS_INPUT_ERROR]);
        }

        try {
            $mAddress = new UserAddress();
            $mAddress->updateAddress($this->cid, $addressId, $title, $address);

        }catch (Exception $e){
            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondOK();

    }

    /**
     * @title("updatesysset")
     * @description("Update User system setting")
     * @requestExample("POST /profile/updatesysset")
     * @response("Data object or Error object")
     */
    public function updateSysSetAction(){
        $isReceiveSMS = $this->request->getPost('receive_sms', 'int');
        $isReceiveEmail = $this->request->getPost('receive_email', 'int');

        if(!isset($this->cid)){
            return $this->respondError(ErrorCodes::AUTH_IDENTITY_MISS, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_IDENTITY_MISS]);
        }

        try {
            $systemSet = new SystemSet();
            $systemSet->set($this->cid, $isReceiveSMS, $isReceiveEmail);

        }catch (Exception $e){
            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondOK();

    }

}