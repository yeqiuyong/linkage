<?php

namespace Multiple\Frontend\Controllers;

use Multiple\Core\FrontendControllerBase;

class ErrorsController extends FrontendControllerBase
{
    public function initialize()
    {
        $this->tag->setTitle('Oops!');
        parent::initialize();
    }

    public function show404Action()
    {

    }

    public function show401Action()
    {

    }

    public function show500Action()
    {

    }
}
