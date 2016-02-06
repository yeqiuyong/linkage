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

}