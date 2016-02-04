<?php

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
