<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/2/16
 * Time: 3:47 PM
 */

namespace Multiple\Models;

use Phalcon\Di;
use Phalcon\Mvc\Model;

class Role extends Model
{
    public function initialize(){
        $this->setSource("linkage_role");
    }

}
