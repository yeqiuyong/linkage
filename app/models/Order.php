<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/2/16
 * Time: 11:18 PM
 */

namespace Multiple\Models;

use Phalcon\Mvc\Model;

class Order extends Model
{
    public function initialize(){
        $this->setSource("linkage_order");

    }

}