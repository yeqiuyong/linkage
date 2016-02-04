<?php
namespace Multiple\API\Controllers;

use Multiple\Core\Exception\Exception;
use Phalcon\Db\RawValue;

use Multiple\Core\APIControllerBase;
use Multiple\Core\Auth\UsernameAdaptor;
use Multiple\Core\Constants\Services;
use Multiple\Models\ClientUser;


/**
 * @resource("User")
 */
class UserController extends APIControllerBase
{

    /**
     * @title("Authenticate")
     * @description("Authenticate user")
     * @headers({
     *      "Authorization": "'Basic sd9u19221934y='"
     * })
     * @requestExample("POST /users/authenticate")
     * @response("Data object or Error object")
     */
    public function registerbynameAction(){
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $smscode = $this->request->getPost('smscode');


        $user = new ClientUser();

        if ($user->registerByName($username, $password) == false) {
            $response = [];
            foreach ($user->getMessages() as $message) {
                array_push($response, (String)$message);
            }
            return $this->respondArray($response, 'data');
        } else {
            $authManager = $this->di->get(Services::AUTH_MANAGER);
            $session = $authManager->loginWithUsernamePassword(UsernameAdaptor::NAME, $username, $password);
            $response = [
                'token' => $session->getToken(),
                'expires' => $session->getExpirationTime()
            ];

            return $this->respondArray($response, 'data');
        }


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
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        try {
            $authManager = $this->di->get(Services::AUTH_MANAGER);
            $session = $authManager->loginWithUsernamePassword(UsernameAdaptor::NAME, $username, $password);

            $response = [
                'token' => $session->getToken(),
                'expires' => $session->getExpirationTime()
            ];

            return $this->respondArray($response, 'data');

        }catch (Exception $e){
            $this->dispatcher->forward(array(
                'controller' => 'errors',
                'action'     => 'show',
                "params" => array("code" => $e->getCode(), "message" => $e->getMessage()),
            ));
        }
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
        $response = [
            'token' => 'aadfaeaa',
            'expires' => 'cccc'
        ];

        $this->respondArray($response,'test');
    }

    public function testAction(){
        $redis = $this->di->get(Services::REDIS);

       // $redis->set("aa", "aaadda");
       // $redis->lpush('pushdaemon', "dddd");

        $response = [
            'token' => $redis->llen('pushdaemon'),

        ];

        $this->respondArray($response,'test');
    }
}
