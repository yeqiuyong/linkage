<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 25/1/16
 * Time: 10:25 AM
 */

namespace Multiple\Core\Exception;

class Exception extends \Exception
{
    public function __construct($code, $message = null)
    {
        $this->code = $code;
        $this->message = $message;
    }
}
