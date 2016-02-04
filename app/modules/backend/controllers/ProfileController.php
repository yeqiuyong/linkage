<?php

namespace Multiple\Backend\Controllers;

use Multiple\Core\BackendControllerBase;
use Multiple\Models\AdminUser;

class ProfileController extends BackendControllerBase
{
    public function initialize()
    {
        $this->tag->setTitle('Welcome');
        parent::initialize();
    }

    public function indexAction(){
        if (!$this->request->isPost()) {
            $this->flash->notice('This is a sample application of the Phalcon Framework.
                Please don\'t provide us any personal information. Thanks');
        }
    }

    public function changeProfileAction(){
        $auth = $this->session->get("auth");
        $username = $auth['username'];
    }

    public function changepwdAction(){
        $old_password = $this->request->getPost('oldpassword');
        $new_password = $this->request->getPost('newpassword');

        $auth = $this->session->get("auth");
        $username = $auth['username'];

        $user = new AdminUser();

        if($user->changePassword($username, $old_password, $new_password)){
            echo "Change Password Successfully!";
        }else{
            echo "change Password failed!";
        }

    }
}
