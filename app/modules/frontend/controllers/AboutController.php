<?php

namespace Multiple\Frontend\Controllers;

use Multiple\Core\FrontendControllerBase;

class AboutController extends FrontendControllerBase
{
    public function initialize()
    {
        $this->tag->setTitle('About us');
        parent::initialize();
    }

    public function indexAction()
    {
    }
}
