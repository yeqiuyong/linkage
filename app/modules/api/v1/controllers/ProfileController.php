<?php
/**
 * Created by PhpStorm.
 * User: uni
 * Date: 16/2/26
 * Time: 下午6:54
 */

namespace Multiple\API\Controllers;

use Multiple\Core\Exception\Exception;
use Phalcon\Db\RawValue;

use Multiple\Core\APIControllerBase;
use Multiple\Core\Auth\UsernameAdaptor;
use Multiple\Core\Constants\Services;
use Multiple\Models\ClientUser;

class ProfileController extends APIControllerBase
{
    public function initialize(){
        parent::initialize();
    }

    /**
     * @title("modPassword")
     * @description("Modify Password")
     * @requestExample("POST /profile/modpassword")
     * @response("Data object or Error object")
     */
    public function modPasswordAction(){

    }

    /**
     * @title("modPassword")
     * @description("Modify Password")
     * @requestExample("POST /profile/modpassword")
     * @response("Data object or Error object")
     */
    public function modMobileAction(){

    }

    /**
     * @title("addCompany")
     * @description("Add company")
     * @requestExample("POST /profile/addcompany")
     * @response("Data object or Error object")
     */
    public function addCompanyAction(){

    }

    /**
     * @title("forgot password")
     * @description("User profile password")
     * @requestExample("POST /profile/forgotpassword")
     * @response("Data object or Error object")
     */
    public function forgotPasswordAction(){

    }
}