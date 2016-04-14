<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 1/2/16
 * Time: 11:50 PM
 */

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
        $admin = new AdminUser();
        $adminInfo = $admin->getUserByName($this->userName);

        $this->view->setVars(
            array(
                'username' => $this->userName,
                'realname' => $adminInfo->name,
                'mobile' => $adminInfo->mobile,
                'email' => $adminInfo->email,
                'profile_name' => $adminInfo->profile->profile_name,
                'update_time' =>date('Y-m-d',$adminInfo->update_time),
            )
        );
    }

    public function changeProfileAction(){
        $realname = $this->request->getPost('realname');
        $mobile = $this->request->getPost('mobile');
        $email = $this->request->getPost('email');

        $user = new AdminUser();
        $user->updateProfile($realname, $mobile, $email);

        return $this->forward('profile/index');
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
