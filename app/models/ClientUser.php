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
use Multiple\Core\Exception\DataBaseException;
use Multiple\Core\Exception\UserOperationException;

class ClientUser extends Model
{
    public $email;

    public function initialize(){
        $this->setSource("linkage_clientuser");

        $this->hasMany('user_id', 'Multiple\Models\ClientUserRole', 'user_id', array(  'alias' => 'user_role',
            'reusable' => true ));

        $this->BelongsTo('company_id', 'Multiple\Models\Company', 'company_id', array(  'alias' => 'company',
            'reusable' => true ));

    }

    public function registerByName($username, $mobile, $password, $status){
        $security = Di::getDefault()->get(Services::SECURITY);

        if($this->isUserNameRegistered($username)){
            throw new UserOperationException(ErrorCodes::USER_DUPLICATE, ErrorCodes::$MESSAGE[ErrorCodes::USER_DUPLICATE]);
        }

        $now = time();

        $this->username = $username;
        $this->mobile = $mobile;
        $this->password = $security->hash($password);
        $this->token = md5($now.$username);
        $this->create_time = $now;
        $this->update_time = $now;
        $this->status = $status;

        if($this->save() == false){
            $message = '';
            foreach ($this->getMessages() as $msg) {
                $message .= (String)$msg;
            }
            $this->logger->debug($message);

            throw new DataBaseException(ErrorCodes::DATA_FAIL, ErrorCodes::$MESSAGE[ErrorCodes::DATA_FAIL]);
        }

    }

    public function registerByMobile($mobile, $password, $status){
        $security = Di::getDefault()->get(Services::SECURITY);

        if($this->isMobileRegistered($mobile)){
            throw new UserOperationException(ErrorCodes::USER_MOBILE_DUPLICATE, ErrorCodes::$MESSAGE[ErrorCodes::USER_MOBILE_DUPLICATE]);
        }

        $now = time();

        $this->username = $mobile;
        $this->mobile = $mobile;
        $this->password = $security->hash($password);

        $this->create_time = $now;
        $this->update_time = $now;
        $this->status = $status;

        if($this->save() == false){
            $message = '';
            foreach ($this->getMessages() as $msg) {
                $message .= (String)$msg;
            }
            $this->logger->debug($message);

            throw new DataBaseException(ErrorCodes::DATA_FAIL, ErrorCodes::$MESSAGE[ErrorCodes::DATA_FAIL]);
        }

    }

    public function getSecretByName($username){
        $user = self::findFirst([
            'conditions' => 'username = :username:',
            'bind' => ['username' => $username]
        ]);

        $password = $user == null ? '' : $user->password;
        $id = $user == null ? '' : $user->user_id;

        return ['password' => $password, 'id' => $id];
    }

    public function getUserByToken($identity){
        $user = self::findFirst([
            'conditions' => 'user_id = :identity:',
            'bind' => ['identity' => $identity]
        ]);

        return $user;
    }

    public function validation(){
        if($this->email){
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


    private function isMobileRegistered($mobile){
        $user = self::findFirst([
            'conditions' => 'mobile = :mobile:',
            'bind' => ['mobile' => $mobile]
        ]);

        return $user == null ? false : true;
    }

    private function isUserNameRegistered($username){
        $user = self::findFirst([
            'conditions' => 'username = :username:',
            'bind' => ['username' => $username]
        ]);

        return $user == null ? false : true;
    }
}
