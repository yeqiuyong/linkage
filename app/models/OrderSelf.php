<?php
/**
 * Created by PhpStorm.
 * User: uni
 * Date: 16/3/27
 * Time: 下午2:34
 */



namespace Multiple\Models;

use Phalcon\Mvc\Model;

class OrderSelf extends Model
{
    public function initialize(){
        $this->setSource("linkage_order_self_cargo");

    }

}