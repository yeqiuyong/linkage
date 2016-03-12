<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 25/1/16
 * Time: 10:25 AM
 */

namespace Multiple\API\Controllers;

use Phalcon\Di;

use Multiple\Core\Exception\Exception;
use Multiple\Core\APIControllerBase;
use Multiple\Core\Auth\UsernameAdaptor;
use Multiple\Core\Constants\Services;
use Multiple\Models\ClientUser;


/**
 * @resource("User")
 */
class UserController extends APIControllerBase
{

    private $logger;

    public function initialize(){
        parent::initialize();

        $this->logger = Di::getDefault()->get(Services::LOGGER);

    }



    /**
     * @title("Authenticate")
     * @description("Authenticate user")
     * @headers({
     *      "Authorization": "'Basic sd9u19221934y='"
     * })
     * @requestExample("POST /users/authenticate")
     * @response("Data object or Error object")
     */
    public function loginAction(){

    }


    /**
     * @title("Me")
     * @description("Get the current user")
     * @includeTypes({
     *      "accounts": "Adds accounts object to the response"
     * })
     * @requestExample("GET /users/me")
     * @response("User object or Error object")
     */
    public function meAction()
    {
//        return $this->respondItem($this->user, new \UserTransformer, 'user');
        $authManager = $this->di->get(Services::AUTH_MANAGER);
        //$token = $authManager->getSession()->getToken();
        $response = [
            'token' => strlen($this->token),
            'expires' => 'caaaaccc'
        ];

        $this->logger->debug('dfaefa');
        $this->respondArray($response,'test');
    }

    public function testAction(){
        $redis = $this->di->get(Services::REDIS);

       // $redis->set("aa", "aaadda");
       // $redis->lpush('pushdaemon', "dddd");

        $response = [
            'token' => $redis->llen('pushdaemon'),

        ];

        $this->respondArray($response);
    }
}
