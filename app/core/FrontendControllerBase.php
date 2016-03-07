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
        $data = array("result" => 0, "reason" => "");
        return $this->response->setJsonContent($data);
    }

    public function responseJsonError($result, $reason){
        $data = array("result" => $result, "reason" => $reason);
        return$this->response->setJsonContent($data);
    }

    public function responseJsonData(Array $data = []){
        $response = array("result" => 0, "reason" => "");
        return $this->response->setJsonContent($response);
    }
}
