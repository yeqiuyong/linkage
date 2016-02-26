<?php
/**
 * Created by PhpStorm.
 * User: uni
 * Date: 16/2/26
 * Time: 下午6:30
 */

namespace Multiple\API\Controllers;

use Multiple\Core\Exception\Exception;
use Phalcon\Db\RawValue;

use Multiple\Core\APIControllerBase;
use Multiple\Core\Auth\UsernameAdaptor;
use Multiple\Core\Constants\Services;
use Multiple\Models\ClientUser;

class SessionController extends APIControllerBase
{
    public function initialize(){
        parent::initialize();
    }

    /**
     * @title("register")
     * @description("User registration")
     * @requestExample("POST /session/register")
     * @response("Data object or Error object")
     */
    public function registerAction(){

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