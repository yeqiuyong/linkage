<?php
namespace Multiple\Core;

use Multiple\Core\ControllerBase;

class BackendControllerBase extends ControllerBase
{

    protected function initialize()
    {
        $this->tag->prependTitle('Linkage | ');
        $this->view->setTemplateAfter('main');
    }

}
