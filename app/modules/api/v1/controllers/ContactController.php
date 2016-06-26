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
    private $logger;

    public function initialize(){
        parent::initialize();

        $this->logger = Di::getDefault()->get(Services::LOGGER);
    }

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
            $userInfo = $user->getUserInfomation($this->cid);

            $mobile = empty($mobile) ? $userInfo['mobile'] : $mobileTmp;
            $email = empty($emailTmp) ? $userInfo['email'] : $emailTmp;
            $name = empty($userInfo['name']) ? $userInfo['username'] : $userInfo['name'];

            $contact = new Contact();
            $contact->add($name, $mobile, $email, $content);

        }catch (Exception $e){
            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondOK();

    }
}