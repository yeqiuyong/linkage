<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/2/16
 * Time: 11:12 PM
 */

namespace Multiple\Models;

use Phalcon\Mvc\Model;

class Car extends Model
{
    public function initialize(){
        $this->setSource("linkage_car");
    }

}