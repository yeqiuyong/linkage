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
    protected $user_name;

    protected $user_profile;

    protected function initialize()
    {
        $this->tag->prependTitle('Linkage | ');
        $this->view->setTemplateAfter('main');
        $this->myProfile();
    }

    private function myProfile(){
        $auth = $this->session->get('auth');
        $this->user_name = $auth['username'];
        $this->user_profile = $auth['profile_name'];

        $this->view->setVars(
            array(
                'username'   => $this->user_name,
                'profilename' => $this->user_profile,
            )
        );
    }

}
