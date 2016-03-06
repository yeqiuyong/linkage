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

    private $logger;

    public function initialize(){
        $this->setSource("linkage_clientuser");

        $this->hasMany('user_id', 'Multiple\Models\ClientUserRole', 'user_id', array(  'alias' => 'user_role',
            'reusable' => true ));

        $this->BelongsTo('company_id', 'Multiple\Models\Company', 'company_id', array(  'alias' => 'company',
            'reusable' => true ));

        $this->logger = Di::getDefault()->get(Services::LOGGER);

    }

    public function registerByName($username, $mobile, $password, $status, $companyID){
        $security = Di::getDefault()->get(Services::SECURITY);

        if($this->isUserNameRegistered($username)){
            throw new UserOperationException(ErrorCodes::COMPANY_DEUPLICATE, ErrorCodes::$MESSAGE[ErrorCodes::COMPANY_DEUPLICATE]);
        }

        $now = time();

        $this->username = $username;
        $this->mobile = $mobile;
        $this->password = $security->hash($password);
        $this->company_id = $companyID;
        $this->create_time = $now;
        $this->update_time = $now;
        $this->status = $status;

        if($this->save() == false){
            $message = '';
            foreach ($this->getMessages() as $msg) {
                $message .= (String)$msg;
            }
            $this->logger->fatal($message);

            throw new DataBaseException(ErrorCodes::DATA_FAIL, ErrorCodes::$MESSAGE[ErrorCodes::DATA_FAIL]);
        }

    }

    public function registerByMobile($mobile, $password, $status, $companyID){
        $security = Di::getDefault()->get(Services::SECURITY);

        if($this->isMobileRegistered($mobile)){
            throw new UserOperationException(ErrorCodes::USER_MOBILE_DUPLICATE, ErrorCodes::$MESSAGE[ErrorCodes::USER_MOBILE_DUPLICATE]);
        }

        $now = time();

        $this->username = $mobile;
        $this->mobile = $mobile;
        $this->password = $security->hash($password);
        $this->company_id = $companyID;

        $this->create_time = $now;
        $this->update_time = $now;
        $this->status = $status;

        if($this->save() == false){
            $message = '';
            foreach ($this->getMessages() as $msg) {
                $message .= (String)$msg;
            }
            $this->logger->fatal($message);

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

    public function getCompanyidByUserid($userid){
        $user = self::findFirst([
            'conditions' => 'user_id = :$userid:',
            'bind' => ['userid' => $userid]
        ]);

        if(!isset($user->company_id)){
            throw new DataBaseException(ErrorCodes::USER_NOTFOUND, ErrorCodes::$MESSAGE[ErrorCodes::USER_NOTFOUND]);
        }

        return $user->company_id;
    }

    public function getUserNameByUserid($userid){
        $user = self::findFirst([
            'conditions' => 'user_id = :$userid:',
            'bind' => ['userid' => $userid]
        ]);

        if(!isset($user->username)){
            throw new DataBaseException(ErrorCodes::USER_NOTFOUND, ErrorCodes::$MESSAGE[ErrorCodes::USER_NOTFOUND]);
        }

        return $user->username;
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
        $users = self::find([
            'conditions' => 'mobile = :mobile:',
            'bind' => ['mobile' => $mobile]
        ]);

        return sizeof($users) > 0 ? true : false;
    }

    private function isUserNameRegistered($username){
        $users = self::findFirst([
            'conditions' => 'username = :username:',
            'bind' => ['username' => $username]
        ]);

        return sizeof($users) > 0 ? true : false;
    }
}
