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

    public function registerByName($username, $password){
        $security = Di::getDefault()->get(Services::SECURITY);

        $now = time();

        $this->username = $username;
        $this->password = $security->hash($password);
        $this->token = md5($now.$username);
        $this->create_time = $now;
        $this->update_time = $now;
        $this->active = 'Y';

        return $this->save();
    }

    public function getSecretByName($username){
        $user = self::findFirst([
            'conditions' => 'username = :username:',
            'bind' => ['username' => $username]
        ]);

        $password = $user == null ? '' : $user->password;
        $userToken = $user == null ? '' : $user->token;

        return ['password' => $password, 'user_token' => $userToken];
    }

    public function getUserByToken($token){
        $user = ClientUser::findFirst([
            'conditions' => 'token = :token:',
            'bind' => ['token' => $token]
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
}
