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
    protected $userName;

    protected $userProfile;

    protected function initialize()
    {
        $this->tag->prependTitle('Linkage | ');
        $this->view->setTemplateAfter('main');
        $this->myProfile();
    }

    private function myProfile(){
        $auth = $this->session->get('auth');
        $this->userName = $auth['username'];
        $this->userProfile = $auth['profile_name'];

        $this->view->setVars(
            array(
                'username'   => $this->userName,
                'profilename' => $this->userProfile,
            )
        );
    }

}
