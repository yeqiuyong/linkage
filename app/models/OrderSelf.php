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
use Multiple\Core\Exception\UserOperationException;

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


    public function getDetail4Manufacture($orderId){
        $phql="select a.transporter_id, a.take_address, a.take_time, a.delivery_address, a.delivery_time, a.is_transfer_port, a.memo, a.is_comment, b.name as company_name, c.is_customs_declare, c.customs_in, c.cargo_take_time from Multiple\Models\Order a join Multiple\Models\Company b join Multiple\Models\OrderSelf c where a.transporter_id = b.company_id and a.order_id = c.order_id and a.order_id = '$orderId'";
        $order = $this->modelsManager->executeQuery($phql);

        if(sizeof($order) == 0){
            throw new UserOperationException(ErrorCodes::ORDER_NOT_FOUND, ErrorCodes::$MESSAGE[ErrorCodes::ORDER_NOT_FOUND]);
        }

        $orderDetail = [
            'company_id' => $order[0]->transporter_id,
            'company_name' => $order[0]->company_name,
            'take_address' => $order[0]->take_address,
            'take_time' => $order[0]->take_time,
            'delivery_address' => $order[0]->delivery_address,
            'delivery_time' => $order[0]->delivery_time,
            'is_transfer_port' => $order[0]->is_transfer_port,
            'memo' => $order[0]->memo,
            'is_customs_declare' => $order[0]->is_customs_declare,
            'customs_in' => $order[0]->customs_in,
            'cargo_take_time' => $order[0]->cargo_take_time,
            'is_comment' => $order[0]->is_comment,

        ];

        return $orderDetail;
    }

    public function getDetail4Transporter($orderId){
        $phql="select a.manufacture_id, a.take_address, a.take_time, a.delivery_address, a.delivery_time, a.is_transfer_port, a.memo, a.is_comment, b.name as company_name, c.is_customs_declare, c.customs_in, c.cargo_take_time from Multiple\Models\Order a join Multiple\Models\Company b join Multiple\Models\OrderSelf c where a.manufacture_id = b.company_id and a.order_id = c.order_id and a.order_id = '$orderId'";
        $order = $this->modelsManager->executeQuery($phql);

        if(sizeof($order) == 0){
            throw new UserOperationException(ErrorCodes::ORDER_NOT_FOUND, ErrorCodes::$MESSAGE[ErrorCodes::ORDER_NOT_FOUND]);
        }

        $orderDetail = [
            'company_id' => $order[0]->manufacture_id,
            'company_name' => $order[0]->company_name,
            'take_address' => $order[0]->take_address,
            'take_time' => $order[0]->take_time,
            'delivery_address' => $order[0]->delivery_address,
            'delivery_time' => $order[0]->delivery_time,
            'is_transfer_port' => $order[0]->is_transfer_port,
            'memo' => $order[0]->memo,
            'is_customs_declare' => $order[0]->is_customs_declare,
            'customs_in' => $order[0]->customs_in,
            'cargo_take_time' => $order[0]->cargo_take_time,
            'is_comment' => $order[0]->is_comment,

        ];

        return $orderDetail;
    }

}