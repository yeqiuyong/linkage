<?php
namespace Multiple\Frontend\Controllers;

use Multiple\Core\FrontendControllerBase;

class IndexController extends FrontendControllerBase
{
    public function initialize()
    {
        $this->tag->setTitle('Welcome');
        parent::initialize();
    }

    public function indexAction()
    {
        if (!$this->request->isPost()) {
            $this->flash->notice('This is a sample application of the Phalcon Framework.
                Please don\'t provide us any personal information. Thanks');
        }
    }
}
