<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 28/1/16
 * Time: 6:02 PM
 */

namespace Multiple\Core\API;

use Multiple\Core\Auth\Session;

interface ITokenParser
{
    /**
     * @param Session $session Session to generate token for
     *
     * @return string Generated token
     */
    public function getToken(Session $session);

    /**
     * @param string $token Access token
     *
     * @return Session Session restored from token
     */
    public function getSession($token);
}