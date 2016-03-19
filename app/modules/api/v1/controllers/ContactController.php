<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 19/3/16
 * Time: 11:17 PM
 */

namespace Multiple\API\Controllers;

use Phalcon\Di;

use Multiple\Core\Exception\Exception;
use Multiple\Core\APIControllerBase;
use Multiple\Core\Constants\ErrorCodes;
use Multiple\Core\Constants\LinkageUtils;
use Multiple\Core\Constants\Services;

use Multiple\Models\ClientUser;
use Multiple\Models\Contact;

class ContactController extends APIControllerBase
{
    /**
     * @title("verifyCode")
     * @description("Get password verify code")
     * @requestExample("POST /code/verifycode")
     * @response("Data object or Error object")
     */
    public function adviceAction(){
        $content = $this->request->getPost('content');
        $mobileTmp = $this->request->getPost('mobile');
        $emailTmp = $this->request->getPost('email');

        if(!isset($content)){
            return $this->respondError(ErrorCodes::USER_COMPLAIN_CONTENT_NULL, ErrorCodes::$MESSAGE[ErrorCodes::USER_COMPLAIN_CONTENT_NULL]);
        }

        try{
            $user = new ClientUser();
            $user->getUserInfomation($this->cid);

            $mobile = empty($mobile) ? $user['mobile'] : $mobileTmp;
            $email = empty($emailTmp) ? $user['email'] : $emailTmp;
            $name = empty($user['name']) ? $user['username'] : $user['name'];

            $contact = new Contact();
            $contact->add($name, $mobile, $email, $contact);

        }catch (Exception $e){
            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondOK();

    }
}