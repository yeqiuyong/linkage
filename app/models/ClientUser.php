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

    public function initialize()
    {
        $this->setSource("linkage_clientuser");
    }

    public function registerByName($username, $password){
        $security = Di::getDefault()->get(Services::SECURITY);

        $this->username = $username;
        $this->password = $security->hash($password);;
        $this->create_time = time();
        $this->update_time = time();
        $this->active = 'Y';

        return $this->save();
    }

    public function validation()
    {
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
