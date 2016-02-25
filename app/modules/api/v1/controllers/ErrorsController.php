<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 25/1/16
 * Time: 10:25 AM
 */

namespace Multiple\API\Controllers;

use Multiple\Core\APIControllerBase;

class ErrorsController extends APIControllerBase
{

    public function showAction()
    {
        //$this->dispatcher->g
        $code = $this->dispatcher->getParam('code');
        $message = $this->dispatcher->getParam('message');

        $response = [
            'result' => $code,
            'reason' => $message,
        ];

        $this->respond($response);
    }


}
