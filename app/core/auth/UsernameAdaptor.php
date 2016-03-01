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

        $user = new ClientUser();
        $secret = $user->getSecretByName($username);

        if(!$security->checkHash($password, $secret['password'])){
            return null;
        }

        return $secret['id'];

    }

    public function authenticate($identity)
    {
        $user = new ClientUser();

        return ($user->getUserByToken($identity) == null ? false: true);
    }
}