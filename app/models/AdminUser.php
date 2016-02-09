<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 28/1/16
 * Time: 6:02 PM
 */

namespace Multiple\Models;

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

    public function initialize()
    {
        $this->setSource("linkage_adminuser");

        $this->hasOne('profile_id', 'Multiple\Models\AdminProfile', 'profile_id', array(  'alias' => 'profile',
            'reusable' => true ));
    }

    public function changePassword($username, $oldpwd, $newpwd){
        $security = Di::getDefault()->get(Services::SECURITY);

        /** @var \User $user */
        $user = AdminUser::findFirst([
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

        $user = AdminUser::findFirst([
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
        $this->active = 'Y';
        $this->create_time = $now;
        $this->update_time = $now;

        if ($this->save() == false){
//            foreach ($this->getMessages() as $message) {
//                echo $message, "\n";
//            }

            throw new DataBaseException(ErrorCodes::DATA_CREATE_FAIL, ErrorCodes::$MESSAGE[ErrorCodes::DATA_CREATE_FAIL]);
        }

    }

    public function setProfileName($profile_name){
        $this->profile_name = $profile_name;
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
