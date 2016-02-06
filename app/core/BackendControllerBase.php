<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 28/1/16
 * Time: 6:02 PM
 */

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
