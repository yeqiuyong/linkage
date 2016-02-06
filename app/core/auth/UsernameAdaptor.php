<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 25/1/16
 * Time: 10:25 AM
 */

namespace Multiple\Core\Auth;

use Phalcon\Di;

use Multiple\Core\Constants\Services;
use Multiple\Core\API\IAuthenticateAdaptor;
use Multiple\Core\Libraries\AuthenticateManager;
use Multiple\Models\ClientUser;

class UsernameAdaptor implements IAuthenticateAdaptor
{
    const NAME = "username";

    public function login($data)
    {
        /** @var \Phalcon\Security $security */
        $security = Di::getDefault()->get(Services::SECURITY);

        $username = $data[AuthenticateManager::LOGIN_DATA_USERNAME];
        $password = $data[AuthenticateManager::LOGIN_DATA_PASSWORD];

        /** @var \User $user */
        $user = ClientUser::findFirst([
            'conditions' => 'username = :username:',
            'bind' => ['username' => $username]
        ]);

        if(!$user){
            return null;
        }

        if(!$security->checkHash($password, $user->password)){
            return null;
        }

        return (string)$user->user_id;

    }

    public function authenticate($identity)
    {
        $user = ClientUser::findFirst([
            'conditions' => 'user_id = :identity:',
            'bind' => ['identity' => $identity]
        ]);

        return ($user == null ? false: true);
    }
}