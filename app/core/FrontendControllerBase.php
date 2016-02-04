<?php
namespace Multiple\Core;

use Multiple\Core\ControllerBase;

class FrontendControllerBase extends ControllerBase
{

    protected function initialize()
    {
        $this->tag->prependTitle('INVO | ');
        $this->view->setTemplateAfter('main');
    }


}
