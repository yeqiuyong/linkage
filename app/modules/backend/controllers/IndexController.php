<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 4/2/16
 * Time: 4:25 PM
 */

namespace Multiple\Backend\Controllers;

use Multiple\Core\BackendControllerBase;

class IndexController extends BackendControllerBase
{
    public function initialize(){
        $this->tag->setTitle('Welcome');
        parent::initialize();
    }

    public function indexAction(){

    }


}
