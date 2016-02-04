<?php
namespace Multiple\Core\Libraries;

/**
 * Created by PhpStorm.
 * User: joe
 * Date: 27/1/16
 * Time: 11:45 AM
 */

use Multiple\Core\Constants\ErrorCodes;
use Multiple\Core\Exception\AuthenticateException;
use Multiple\Core\API\IAuthenticateAdaptor;
use Multiple\Core\API\ITokenParser;
use Multiple\Core\Auth\Session;

class AuthenticateManager
{
    const LOGIN_DATA_USERNAME = "username";
    const LOGIN_DATA_PASSWORD = "password";

    /**
     * @var AccountType[] Account types
     */
    protected $adaptor;



    /**
     * @var tokenParser[] Token Parser
     */
    protected $tokenParser;

    /**
     * @var Session Currenty active session
     */
    protected $session;

    /**
     * @var int Expiration time of created sessions
     */
    protected $sessionDuration;


    public function __construct($sessionDuration = 86400)
    {
        $this->sessionDuration = $sessionDuration;

        $this->adaptor = [];
        $this->session = null;
    }


    public function registerAdaptor($name, IAuthenticateAdaptor $account)
    {
        $this->adaptor[$name] = $account;

        return $this;
    }

    public function registerTokenParser(ITokenParser $parser){
        $this->tokenParser = $parser;
    }

    public function getAdaptor()
    {
        return $this->adaptor;
    }


    public function getSessionDuration()
    {
        return $this->sessionDuration;
    }

    public function setSessionDuration($time)
    {
        $this->sessionDuration = $time;
    }


    public function getSession()
    {
        return $this->session;
    }

    public function setSession(Session $session)
    {
        $this->session = $session;
    }


    /**
     * @return bool
     *
     * Check if a user is currently logged in
     */
    public function loggedIn()
    {
        return !!$this->session;
    }

    /**
     * @param $name
     *
     * @return
     */
    public function getAdaptorByName($name)
    {
        if (array_key_exists($name, $this->adaptor)) {

            return $this->adaptor[$name];
        }

        return false;
    }


    /**
     * @param string $adaptorName
     * @param array $data
     *
     * @return Session Created session
     * @throws AuthenticateException
     *
     * Login a user with the specified account-type
     */
    public function login($adaptorName, array $data)
    {
        if (!$adaptor = $this->getAdaptorByName($adaptorName)) {

            throw new AuthenticateException(ErrorCodes::AUTH_INVALIDTYPE, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_INVALIDTYPE]);
        }

        $identity = $adaptor->login($data);

        if (!$identity) {
            throw new AuthenticateException(ErrorCodes::AUTH_BADLOGIN, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_BADLOGIN]);
        }

        $startTime = time();

        $session = new Session($adaptorName, $identity, $startTime, $startTime + $this->sessionDuration);
        $token = $this->tokenParser->getToken($session);
        $session->setToken($token);

        $this->session = $session;

        return $this->session;
    }

    /**
     * @param string $accountTypeName
     * @param string $username
     * @param string $password
     *
     * @return Session Created session
     * @throws AuthenticateException
     *
     * Helper to session with username & password
     */
    public function loginWithUsernamePassword($accountTypeName, $username, $password)
    {
        return $this->login($accountTypeName, [
            self::LOGIN_DATA_USERNAME => $username,
            self::LOGIN_DATA_PASSWORD => $password,
        ]);
    }

    /**
     * @param string $token Token to authenticate with
     *
     * @return bool
     * @throws AuthenticateException
     */
    public function authenticateToken($token)
    {
        try {

            $session = $this->tokenParser->getSession($token);
        }
        catch(\Exception $e){

            throw new AuthenticateException(ErrorCodes::AUTH_BADTOKEN,ErrorCodes::$MESSAGE[ErrorCodes::AUTH_BADTOKEN]);
        }

        if(!$session){
            return false;
        }

        if($session->getExpirationTime() < time()){

            throw new AuthenticateException(ErrorCodes::AUTH_EXPIRED,ErrorCodes::$MESSAGE[ErrorCodes::AUTH_EXPIRED]);
        }

        $session->setToken($token);

        // Authenticate identity
        if (!$account = $this->getAdaptorByName($session->getAdaptorByName())) {

            throw new AuthenticateException(ErrorCodes::DATA_NOTFOUND,ErrorCodes::$MESSAGE[ErrorCodes::DATA_NOTFOUND]);
        }

        if($account->authenticate($session->getIdentity())){

            $this->session = $session;
        }

        return !!$this->session;

    }
}