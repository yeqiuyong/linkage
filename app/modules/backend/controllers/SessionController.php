<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 1/2/16
 * Time: 11:50 PM
 */

namespace Multiple\Backend\Controllers;

use Phalcon\Di;

use Multiple\Core\ControllerBase;
use Multiple\Core\Constants\Services;
use Multiple\Models\AdminUser;


class SessionController extends ControllerBase{

    public function initialize(){
    }

    public function indexAction(){
    }


    /**
     * This action authenticate and logs an user into the application
     *
     */
    public function loginAction()
    {
        if (!$this->request->isPost()) {
            return $this->forward('session/index');
        }

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        /** @var \User $user */
        $user = AdminUser::findFirst([
            'conditions' => 'username = :username:',
            'bind' => ['username' => $username]
        ]);

        $security = Di::getDefault()->get(Services::SECURITY);

        if(!$security->checkHash($password, $user->password)){
            $this->flash->error('Wrong email/password');
        }else{
            if ($user != false) {
                $this->_registerSession($user);
                $this->flash->success('Welcome ' . $user->username);
                return $this->forward('index/index');
            }
        }
    }

    /**
     * Finishes the active session redirecting to the index
     *
     * @return unknown
     */
    public function logoutAction()
    {
        $this->session->remove('auth');
        $this->flash->success('Goodbye!');
        return $this->forward('session/index');
    }



    /**
     * Register an authenticated user into session data
     *
     * @param Users $user
     */
    private function _registerSession(AdminUser $user)
    {
        $this->session->set('auth', array(
            'id' => $user->id,
            'username' => $user->username
        ));
    }
}