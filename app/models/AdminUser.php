<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 28/1/16
 * Time: 6:02 PM
 */

namespace Multiple\Models;

use Multiple\Core\Constants\LinkageUtils;
use Multiple\Core\Constants\StatusCodes;
use Phalcon\Di;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Validator\Email as EmailValidator;
use Phalcon\Mvc\Model\Validator\Uniqueness as UniquenessValidator;

use Multiple\Core\Constants\Services;
use Multiple\Core\Constants\ErrorCodes;
use Multiple\Core\Exception\UserOperationException;
use Multiple\Core\Exception\DataBaseException;

class AdminUser extends Model
{
    //public $profile_id;

    public  $profile_name;

    //private $email;

    public function initialize(){
        $this->setSource("linkage_adminuser");

        $this->hasOne('profile_id', 'Multiple\Models\AdminProfile', 'profile_id', array(  'alias' => 'profile',
            'reusable' => true ));
    }

    public function changePassword($username, $oldpwd, $newpwd){
        $security = Di::getDefault()->get(Services::SECURITY);

        /** @var \User $user */
        $user = self::findFirst([
            'conditions' => 'username = :username:',
            'bind' => ['username' => $username]
        ]);

        if(!$security->checkHash($oldpwd, $user->password)){
            return false;
        }

        $user->password = $security->hash($newpwd);

        return $user->update();

    }

    public function add($username, $password, $realname, $mobile , $email){
        $security = Di::getDefault()->get(Services::SECURITY);

        $user = self::findFirst([
            'conditions' => 'username = :username:',
            'bind' => ['username' => $username]
        ]);

        if($user != null){
            throw new UserOperationException(ErrorCodes::USER_DUPLICATE, ErrorCodes::$MESSAGE[ErrorCodes::USER_DUPLICATE]);
        }

        $now = time();

        $this->username = $username;
        $this->password = $security->hash($password);;
        $this->name = $realname;
        $this->mobile = $mobile;
        $this->email = $email;
        $this->profile_id = 2;
        $this->token = '';
        $this->login = '127.0.0.1';
        $this->status = StatusCodes::ADMIN_USER_ACTIVE;
        $this->create_time = $now;
        $this->update_time = $now;

        if ($this->save() == false){
            $message = '';
            foreach ($this->getMessages() as $msg) {
                $message .= (String)$msg. ',';
            }
            $logger = Di::getDefault()->get(Services::LOGGER);
            $logger->fatal($message);

            throw new DataBaseException(ErrorCodes::DATA_CREATE_FAIL, ErrorCodes::$MESSAGE[ErrorCodes::DATA_CREATE_FAIL]);
        }

    }

    public function setProfileName($profile_name){
        $this->profile_name = $profile_name;
    }

    public function getAdmins(){
        $results = [];
        $users = self::find([
                'conditions' => 'status != :status:',
                'bind' => ['status' => StatusCodes::ADMIN_USER_DELETED]
            ]
        );
        foreach ($users as $user) {
            $result = [];

            $result['id'] = $user->admin_id;
            $result['username'] = $user->username;
            $result['create_time'] = $user->create_time;
            $result['status'] = $user->status;
            $result['profile_name'] = $user->profile->profile_name;

            array_push($results,$result);
        }

        return $results;
    }

    public function getUserByName($userName){
        $user = self::findFirst([
            'conditions' => 'username = :username:',
            'bind' => ['username' => $userName]
        ]);

        if(!isset($user->admin_id)){
            throw new UserOperationException(ErrorCodes::USER_NOTFOUND, ErrorCodes::$MESSAGE[ErrorCodes::USER_NOTFOUND]);
        }

        return $user;
    }

    public function getUserById($adminId){
        $user = self::findFirst([
            'conditions' => 'admin_id = :admin_id:',
            'bind' => ['admin_id' => $adminId]
        ]);

        if(!isset($user->admin_id)){
            throw new UserOperationException(ErrorCodes::USER_NOTFOUND, ErrorCodes::$MESSAGE[ErrorCodes::USER_NOTFOUND]);
        }

        $result['id'] = $user->admin_id;
        $result['username'] = $user->username;
        $result['realname'] = $user->name;
        $result['update_time'] = $user->update_time;
        $result['mobile'] = $user->mobile;
        $result['email'] = $user->email;

        return $result;
    }

    public function updateProfile($realname, $mobile, $email){
        $user = self::findFirst([
            'conditions' => 'username = :username:',
            'bind' => ['username' => $this->userName]
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

        if ($this->update() == false){
            $message = '';
            foreach ($this->getMessages() as $msg) {
                $message .= (String)$msg. ',';
            }
            $logger = Di::getDefault()->get(Services::LOGGER);
            $logger->fatal($message);

            throw new DataBaseException(ErrorCodes::DATA_CREATE_FAIL, ErrorCodes::$MESSAGE[ErrorCodes::DATA_CREATE_FAIL]);
        }
    }


    public function updateStatus($adminId, $status){
        $admin = self::findFirst([
            'conditions' => 'admin_id = :admin_id:',
            'bind' => ['admin_id' => $adminId]
        ]);

        if(!isset($admin->admin_id)){
            throw new UserOperationException(ErrorCodes::USER_NOTFOUND, ErrorCodes::$MESSAGE[ErrorCodes::USER_NOTFOUND]);
        }

        $admin->status = $status;
        $admin->update_time = time();

        if($admin->update() == false){
            $message = '';
            foreach ($admin->getMessages() as $msg) {
                $message .= (String)$msg. ',';
            }
            $logger = Di::getDefault()->get(Services::LOGGER);
            $logger->fatal($message);

            throw new DataBaseException(ErrorCodes::DATA_FAIL, ErrorCodes::$MESSAGE[ErrorCodes::DATA_FAIL]);
        }
    }

    public function validation()
    {
        if( $this->email ){
            $this->validate(new EmailValidator(array(
                'field' => 'email'
            )));
        }
        $this->validate(new UniquenessValidator(array(
            'field' => 'username',
            'message' => 'Sorry, That username is already taken'
        )));
        if ($this->validationHasFailed() == true) {
            return false;
        }
    }
}
