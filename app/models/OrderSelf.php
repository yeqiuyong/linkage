<?php
/**
 * Created by PhpStorm.
 * User: uni
 * Date: 16/3/27
 * Time: 下午2:34
 */



namespace Multiple\Models;

use Phalcon\Di;
use Phalcon\Mvc\Model;

use Multiple\Core\Constants\Services;
use Multiple\Core\Constants\ErrorCodes;
use Multiple\Core\Exception\DataBaseException;

class OrderSelf extends Model
{
    public function initialize(){
        $this->setSource("linkage_order_self_cargo");

    }

    public function add($orderId, $customsIn, $cargoTakeTime, $isCustomsDeclare){
        $this->order_id = $orderId;
        $this->customs_in = $customsIn;
        $this->cargo_take_time = $cargoTakeTime;
        $this->is_customs_declare = $isCustomsDeclare;

        if($this->save() == false){
            $message = '';
            foreach ($this->getMessages() as $msg) {
                $message .= (String)$msg . ",";
            }
            $logger = Di::getDefault()->get(Services::LOGGER);
            $logger->fatal($message);

            throw new DataBaseException(ErrorCodes::DATA_FAIL, ErrorCodes::$MESSAGE[ErrorCodes::DATA_FAIL]);
        }
    }

}