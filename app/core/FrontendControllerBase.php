<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 28/1/16
 * Time: 6:02 PM
 */

namespace Multiple\Core;

use Multiple\Core\ControllerBase;

class FrontendControllerBase extends ControllerBase
{

    protected function initialize()
    {
        $this->tag->prependTitle('INVO | ');
        $this->view->setTemplateAfter('main');
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
