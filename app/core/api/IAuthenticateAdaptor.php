<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 28/1/16
 * Time: 5:38 PM
 */

namespace Multiple\Core\API;

interface IAuthenticateAdaptor
{
    /**
     * @param array $data Login data
     *
     * @return string Identity
     */
    public function login($data);

    /**
     * @param string $identity Identity
     *
     * @return bool Authentication successful
     */
    public function authenticate($identity);
}