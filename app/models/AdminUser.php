<?php
namespace Multiple\Models;

use Phalcon\Di;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Validator\Email as EmailValidator;
use Phalcon\Mvc\Model\Validator\Uniqueness as UniquenessValidator;

use Multiple\Core\Constants\Services;

class AdminUser extends Model
{
    private $email;

    public function initialize()
    {
        $this->setSource("linkage_adminuser");
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
