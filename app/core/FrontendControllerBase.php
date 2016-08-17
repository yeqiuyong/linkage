<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 28/1/16
 * Time: 6:02 PM
 */

namespace Multiple\Core;

use Multiple\Core\ControllerBase;

class FrontendControllerBase extends ControllerBase
{


    protected function initialize()
    {
        $this->cid = $this->request->getPost('cid');
        $this->token = $this->request->getPost('token');

        $this->tag->prependTitle('INVO | ');
        $this->view->setTemplateAfter('main');
    }

}
