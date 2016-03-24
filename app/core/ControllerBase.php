<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 2/2/16
 * Time: 1:49 PM
 */

namespace Multiple\Core;

use Phalcon\Mvc\Controller;

class ControllerBase extends Controller{

    protected function forward($uri)
    {
        $uriParts = explode('/', $uri);
        $params = array_slice($uriParts, 2);
        return $this->dispatcher->forward(
            array(
                'controller' => $uriParts[0],
                'action' => $uriParts[1],
                'params' => $params
            )
        );
    }

    public function responseJsonOK(){
        $response = ['code' => 0, 'stackTrace' => '', 'message' => ''];
        return $this->response->setJsonContent($response);
    }

    public function responseJsonError($code, $message){
        $response = ['code' => $code, 'stackTrace' => '', 'message' => $message];
        return$this->response->setJsonContent($response);
    }

    public function responseJsonData(Array $data = []){
        $response = array("code" => 0,  'stackTrace' => '', "message" => "");
        return $this->response->setJsonContent(array_merge($response, $data));
    }

}