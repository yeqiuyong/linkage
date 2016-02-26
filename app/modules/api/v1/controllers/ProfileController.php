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
     * @title("information")
     * @description("user information")
     * @requestExample("POST /profile/information")
     * @response("Data object or Error object")
     */
    public function informationAction(){

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

    }

    /**
     * @title("modmobile")
     * @description("Modify Mobile")
     * @requestExample("POST /profile/modmobile")
     * @response("Data object or Error object")
     */
    public function modMobileAction(){

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