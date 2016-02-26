<?php
/**
 * Created by PhpStorm.
 * User: uni
 * Date: 16/2/26
 * Time: 下午7:55
 */


namespace Multiple\API\Controllers;

use Multiple\Core\Exception\Exception;
use Phalcon\Db\RawValue;

use Multiple\Core\APIControllerBase;
use Multiple\Core\Auth\UsernameAdaptor;
use Multiple\Core\Constants\Services;
use Multiple\Models\ClientUser;

class CodeController extends APIControllerBase
{
    public function initialize(){
        parent::initialize();
    }

    /**
     * @title("register")
     * @description("Get password verify code")
     * @requestExample("POST /code/verifycode")
     * @response("Data object or Error object")
     */
    public function verifyCodeAction(){

    }

    /**
     * @title("login")
     * @description("Get user invite code")
     * @requestExample("POST /code/invitecode")
     * @response("Data object or Error object")
     */
    public function inviteCodeAction(){

    }

}