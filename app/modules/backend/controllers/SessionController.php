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
        if ($this->request->isPost()) {
            $password = $this->request->getPost("password", "string");
            $username = $this->request->getPost("username", "string");

            if (empty($username) || empty($password)) {
                $data = array("result" => 1, "msg" => "用户名、密码不能为空！", "success" => false);
            } else {
                $user = AdminUser::findFirst([
                    'conditions' => 'username = :username:',
                    'bind' => ['username' => $username]
                ]);

                if ($user) {
                    $security = Di::getDefault()->get(Services::SECURITY);

                    if (!$security->checkHash($password, $user->password)) {
                        $data = array("msg" => "密码错误，请重试！", "status" => 1);
                    } else {
                        if ($user != false) {
                            $this->_registerSession($user);
                            $data = array("msg" => "登陆成功！", "status" => 0, "url" => "/admin/index/index");
                        } else {
                            $data = array("msg" => "用户名不存在，请重试！", "status" => 1);
                        }
                    }
                }
            }
            return $this->response->setJsonContent($data);
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
            'username' => $user->username,
            'profile_id' => $user->profile_id,
            'profile_name' => $user->profile->profile_name,
        ));
    }
}