<?php
namespace Multiple\API\services;


class UserService
{
    protected $user = false;

    /**
     * @return \User
     * @throws UserException
     */
    public function getUser()
    {
        if($this->user === false){

            $user = null;

            $session = $this->authManager->getSession();
            if($session){

                $identity = $session->getIdentity();
                $user = \User::findFirst((int)$identity);
            }

            $this->user = $user;
        }

        return $this->user;
    }
}
