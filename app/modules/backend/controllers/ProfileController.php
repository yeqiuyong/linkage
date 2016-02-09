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
        $user = AdminUser::findFirst([
            'conditions' => 'username = :username:',
            'bind' => ['username' => $this->user_name]
        ]);

        $this->view->setVars(
            array(
                'username' => $this->user_name,
                'realname' => $user->name,
                'mobile' => $user->mobile,
                'email' => $user->email,
                'profile_name' => $user->profile->profile_name,
                'update_time' =>date('Y-m-d',$user->update_time),
            )
        );
    }

    public function changeProfileAction(){
        $realname = $this->request->getPost('realname');
        $mobile = $this->request->getPost('mobile');
        $email = $this->request->getPost('email');

        $user = AdminUser::findFirst([
            'conditions' => 'username = :username:',
            'bind' => ['username' => $this->user_name]
        ]);

        if($realname){
            $user->name = $realname;
        }

        if($mobile){
            $user->mobile = $mobile;
        }

        if($email){
            $user->email = $email;
        }

        $user->update();

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
